import React, { useState, useEffect } from 'react';
import { Card, Row, Col, Button, Spinner, Alert, Form } from 'react-bootstrap';
import { FiUploadCloud, FiFile, FiCheck, FiAlertCircle } from 'react-icons/fi';
import { useCallback } from 'react';
import { useDropzone } from 'react-dropzone';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge';

export default function UploadPage() {
  const [campaigns, setCampaigns] = useState([]);
  const [selectedCampaign, setSelectedCampaign] = useState('');
  const [assets, setAssets] = useState([]);
  const [uploading, setUploading] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);

  useEffect(() => {
    const fetchCampaigns = async () => {
      try {
        const res = await api.get('/invitations/mine');
        const accepted = (res.data.data || []).filter(
          inv => inv.status === 'interested' || inv.offer_status === 'accepted'
        );
        setCampaigns(accepted);
      } catch (err) {
        setError('Failed to load campaigns');
      } finally {
        setLoading(false);
      }
    };
    fetchCampaigns();
  }, []);

  useEffect(() => {
    if (!selectedCampaign) return;
    const fetchAssets = async () => {
      try {
        const res = await api.get(`/campaigns/${selectedCampaign}/assets`);
        setAssets(res.data.data || []);
      } catch (err) {
        // ignore
      }
    };
    fetchAssets();
  }, [selectedCampaign]);

  const onDrop = useCallback(async (acceptedFiles) => {
    if (!selectedCampaign) {
      setError('Please select a campaign first');
      return;
    }
    setUploading(true);
    setError(null);
    setSuccess(null);
    try {
      for (const file of acceptedFiles) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('campaign_id', selectedCampaign);
        formData.append('asset_type', 'content');
        await api.post('/assets/upload', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      }
      setSuccess(`${acceptedFiles.length} file(s) uploaded successfully!`);
      // Refresh assets
      const res = await api.get(`/campaigns/${selectedCampaign}/assets`);
      setAssets(res.data.data || []);
    } catch (err) {
      setError('Upload failed. Please try again.');
    } finally {
      setUploading(false);
    }
  }, [selectedCampaign]);

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    onDrop,
    accept: {
      'image/*': ['.jpeg', '.jpg', '.png', '.gif', '.webp'],
      'video/*': ['.mp4', '.mov', '.avi', '.webm'],
      'application/pdf': ['.pdf'],
    },
    maxSize: 100 * 1024 * 1024, // 100MB
  });

  if (loading) return <div className="text-center py-5"><Spinner animation="border" /></div>;

  return (
    <div>
      <div style={{ marginBottom: 24 }}>
        <h4 style={{ fontWeight: 700 }}>Upload Content</h4>
        <p style={{ color: 'var(--color-text-muted)', fontSize: 14 }}>
          Upload your campaign content for review
        </p>
      </div>

      {error && <Alert variant="danger" dismissible onClose={() => setError(null)}>{error}</Alert>}
      {success && <Alert variant="success" dismissible onClose={() => setSuccess(null)}>{success}</Alert>}

      <Row className="g-4">
        <Col xs={12} lg={8}>
          <Card className="card" style={{ padding: 24 }}>
            <Form.Group className="mb-4">
              <Form.Label>Select Campaign</Form.Label>
              <Form.Select
                value={selectedCampaign}
                onChange={(e) => setSelectedCampaign(e.target.value)}
              >
                <option value="">Choose a campaign...</option>
                {campaigns.map(inv => (
                  <option key={inv.campaign_id} value={inv.campaign_id}>
                    {inv.campaign_title || `Campaign #${inv.campaign_id}`}
                  </option>
                ))}
              </Form.Select>
            </Form.Group>

            <div
              {...getRootProps()}
              style={{
                border: `2px dashed ${isDragActive ? 'var(--color-primary)' : 'var(--color-border)'}`,
                borderRadius: 16,
                padding: 48,
                textAlign: 'center',
                cursor: 'pointer',
                background: isDragActive ? 'rgba(37, 76, 203, 0.04)' : '#fafafa',
                transition: 'all 0.2s',
              }}
            >
              <input {...getInputProps()} />
              {uploading ? (
                <Spinner animation="border" variant="primary" />
              ) : (
                <>
                  <FiUploadCloud size={48} color="var(--color-primary)" style={{ marginBottom: 16 }} />
                  <p style={{ fontWeight: 600, fontSize: 16, marginBottom: 4 }}>
                    {isDragActive ? 'Drop files here...' : 'Drag & drop your content here'}
                  </p>
                  <p style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 0 }}>
                    or click to browse. Supports images, videos, and PDFs (max 100MB)
                  </p>
                </>
              )}
            </div>
          </Card>
        </Col>

        <Col xs={12} lg={4}>
          <Card className="card" style={{ padding: 24 }}>
            <h6 style={{ fontWeight: 600, marginBottom: 16 }}>Uploaded Files</h6>
            {assets.length === 0 ? (
              <p style={{ fontSize: 13, color: 'var(--color-text-muted)' }}>
                No files uploaded yet for this campaign.
              </p>
            ) : (
              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                {assets.map((asset) => (
                  <div
                    key={asset.id}
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      gap: 12,
                      padding: 12,
                      background: '#f9fafb',
                      borderRadius: 10,
                    }}
                  >
                    <div style={{
                      width: 36,
                      height: 36,
                      borderRadius: 8,
                      background: asset.status === 'approved' ? '#dcfce7' :
                        asset.status === 'revision_requested' ? '#fef3c7' : '#f3f4f6',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                    }}>
                      {asset.status === 'approved' ? <FiCheck color="#22c55e" /> :
                        asset.status === 'revision_requested' ? <FiAlertCircle color="#f59e0b" /> :
                          <FiFile color="#6b7280" />}
                    </div>
                    <div style={{ flex: 1, minWidth: 0 }}>
                      <div style={{
                        fontSize: 13,
                        fontWeight: 500,
                        overflow: 'hidden',
                        textOverflow: 'ellipsis',
                        whiteSpace: 'nowrap',
                      }}>
                        {asset.file_name}
                      </div>
                      <StatusBadge status={asset.status} />
                    </div>
                  </div>
                ))}
              </div>
            )}
          </Card>
        </Col>
      </Row>
    </div>
  );
}
