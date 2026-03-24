import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { Tab, Tabs, Button, Alert, Form, Row, Col } from 'react-bootstrap';
import { FiCalendar, FiDollarSign, FiDownload } from 'react-icons/fi';
import { useAuth } from '../../context/AuthContext';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge';
import OfferCard from '../../components/OfferCard';
import UserSelector from '../../components/UserSelector';
import FileUpload from '../../components/FileUpload';
import LoadingSpinner from '../../components/LoadingSpinner';

export default function CampaignDetailPage() {
  const { id } = useParams();
  const { user } = useAuth();
  const [campaign, setCampaign] = useState(null);
  const [invitations, setInvitations] = useState([]);
  const [offers, setOffers] = useState([]);
  const [assets, setAssets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [offerForm, setOfferForm] = useState({ influencer_id: '', amount: '', message: '' });

  useEffect(() => {
    fetchAll();
  }, [id]);

  const fetchAll = async () => {
    setLoading(true);
    try {
      const [campRes] = await Promise.allSettled([
        api.get(`/campaigns/${id}`),
      ]);

      if (campRes.status === 'fulfilled') {
        setCampaign(campRes.value.data);
      }

      const [invRes, offRes, assRes] = await Promise.allSettled([
        api.get(`/campaigns/${id}/invitations`),
        api.get(`/campaigns/${id}/offers`),
        api.get(`/campaigns/${id}/assets`),
      ]);

      if (invRes.status === 'fulfilled') setInvitations(invRes.value.data || []);
      if (offRes.status === 'fulfilled') setOffers(offRes.value.data || []);
      if (assRes.status === 'fulfilled') setAssets(assRes.value.data || []);
    } catch {
      // handled
    } finally {
      setLoading(false);
    }
  };

  const handleAction = async (action) => {
    setActionLoading(true);
    setError('');
    setSuccess('');
    try {
      await api.put(`/campaigns/${id}/${action}`);
      setSuccess(`Campaign ${action}d successfully`);
      fetchAll();
    } catch (err) {
      setError(err.response?.data?.message || `Failed to ${action} campaign`);
    } finally {
      setActionLoading(false);
    }
  };

  const handleInvitationResponse = async (invitationId, response) => {
    try {
      await api.put(`/invitations/${invitationId}`, { status: response });
      setSuccess(`Invitation ${response} successfully`);
      fetchAll();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to respond to invitation');
    }
  };

  const handleSendOffer = async (e) => {
    e.preventDefault();
    try {
      await api.post(`/campaigns/${id}/offers`, offerForm);
      setSuccess('Offer sent successfully');
      setOfferForm({ influencer_id: '', amount: '', message: '' });
      fetchAll();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to send offer');
    }
  };

  const handleAssetAction = async (assetId, action) => {
    try {
      await api.put(`/assets/${assetId}/${action}`);
      setSuccess(`Content ${action}d successfully`);
      fetchAll();
    } catch (err) {
      setError(err.response?.data?.message || `Failed to ${action} content`);
    }
  };

  const handleContentUpload = async (files) => {
    if (!files || files.length === 0) return;
    const formData = new FormData();
    files.forEach((file) => formData.append('files', file));
    formData.append('campaign_id', id);
    try {
      await api.post('/assets/upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      setSuccess('Content uploaded successfully');
      fetchAll();
    } catch (err) {
      setError(err.response?.data?.message || 'Upload failed');
    }
  };

  if (loading) return <LoadingSpinner />;
  if (!campaign) return <div style={{ textAlign: 'center', padding: 64, color: 'var(--color-text-muted)' }}>Campaign not found</div>;

  const isAdmin = user?.role === 'admin';
  const isBrand = user?.role === 'brand';
  const isInfluencer = user?.role === 'influencer';

  return (
    <div>
      {error && <Alert variant="danger" dismissible onClose={() => setError('')} style={{ borderRadius: 8 }}>{error}</Alert>}
      {success && <Alert variant="success" dismissible onClose={() => setSuccess('')} style={{ borderRadius: 8 }}>{success}</Alert>}

      {/* Header */}
      <div style={{
        background: 'var(--color-card-bg)',
        borderRadius: 'var(--radius-lg)',
        boxShadow: 'var(--shadow-md)',
        padding: '28px 32px',
        marginBottom: 24,
      }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: 16 }}>
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 8 }}>
              <h3 style={{ fontWeight: 700, margin: 0 }}>{campaign.title}</h3>
              <StatusBadge status={campaign.status} />
            </div>
            <div style={{ display: 'flex', gap: 20, fontSize: 14, color: 'var(--color-text-muted)', flexWrap: 'wrap' }}>
              {campaign.budget && (
                <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                  <FiDollarSign size={15} />
                  <strong style={{ color: 'var(--color-text)' }}>${Number(campaign.budget).toLocaleString()}</strong>
                </span>
              )}
              {(campaign.start_date || campaign.end_date) && (
                <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                  <FiCalendar size={15} />
                  {campaign.start_date && new Date(campaign.start_date).toLocaleDateString()} - {campaign.end_date && new Date(campaign.end_date).toLocaleDateString()}
                </span>
              )}
            </div>
          </div>
          <div style={{ display: 'flex', gap: 8 }}>
            {isAdmin && campaign.status === 'pending_approval' && (
              <>
                <Button variant="success" onClick={() => handleAction('approve')} disabled={actionLoading}>
                  Approve
                </Button>
                <Button variant="danger" onClick={() => handleAction('reject')} disabled={actionLoading}>
                  Reject
                </Button>
              </>
            )}
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div style={{
        background: 'var(--color-card-bg)',
        borderRadius: 'var(--radius-lg)',
        boxShadow: 'var(--shadow-md)',
        padding: '24px 32px',
      }}>
        <Tabs defaultActiveKey="overview" className="mb-4">
          {/* Overview Tab */}
          <Tab eventKey="overview" title="Overview">
            <div style={{ maxWidth: 700 }}>
              <h6 style={{ fontWeight: 600, marginBottom: 8 }}>Description</h6>
              <p style={{ color: 'var(--color-text-muted)', lineHeight: 1.7, whiteSpace: 'pre-wrap' }}>
                {campaign.description || 'No description provided.'}
              </p>

              {campaign.target_audience && (
                <>
                  <h6 style={{ fontWeight: 600, marginTop: 20, marginBottom: 8 }}>Target Audience</h6>
                  <p style={{ color: 'var(--color-text-muted)' }}>{campaign.target_audience}</p>
                </>
              )}

              {campaign.countries && campaign.countries.length > 0 && (
                <>
                  <h6 style={{ fontWeight: 600, marginTop: 20, marginBottom: 8 }}>Target Countries</h6>
                  <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                    {(typeof campaign.countries === 'string' ? JSON.parse(campaign.countries) : campaign.countries).map((c) => (
                      <span key={c} style={{
                        padding: '4px 12px', borderRadius: 20, background: '#f3f4f6',
                        fontSize: 13, fontWeight: 500,
                      }}>
                        {c}
                      </span>
                    ))}
                  </div>
                </>
              )}

              {campaign.briefing_url && (
                <div style={{ marginTop: 24 }}>
                  <Button
                    variant="outline-primary"
                    href={campaign.briefing_url}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <FiDownload size={16} style={{ marginRight: 8 }} />
                    Download Briefing
                  </Button>
                </div>
              )}
            </div>
          </Tab>

          {/* Influencers Tab */}
          <Tab eventKey="influencers" title="Influencers">
            {invitations.length > 0 ? (
              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                {invitations.map((inv) => (
                  <div
                    key={inv.id}
                    style={{
                      display: 'flex',
                      justifyContent: 'space-between',
                      alignItems: 'center',
                      padding: '14px 20px',
                      borderRadius: 'var(--radius-md)',
                      border: '1px solid var(--color-border)',
                      flexWrap: 'wrap',
                      gap: 12,
                    }}
                  >
                    <div>
                      <span style={{ fontWeight: 600, fontSize: 14 }}>
                        {inv.influencer_name || inv.influencer?.display_name || `Influencer #${inv.influencer_id}`}
                      </span>
                      {inv.influencer?.niche && (
                        <span style={{ marginLeft: 8, fontSize: 12, color: 'var(--color-text-muted)' }}>
                          {inv.influencer.niche}
                        </span>
                      )}
                    </div>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                      <StatusBadge status={inv.status} />
                      {isInfluencer && inv.status === 'invited' && (
                        <>
                          <Button size="sm" variant="success" onClick={() => handleInvitationResponse(inv.id, 'interested')}>
                            Interested
                          </Button>
                          <Button size="sm" variant="outline-danger" onClick={() => handleInvitationResponse(inv.id, 'declined')}>
                            Decline
                          </Button>
                        </>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p style={{ color: 'var(--color-text-muted)', textAlign: 'center', padding: 24 }}>
                No influencers invited yet
              </p>
            )}

            {isAdmin && (
              <div style={{ marginTop: 24, paddingTop: 24, borderTop: '1px solid var(--color-border)' }}>
                <UserSelector campaignId={id} onInvite={fetchAll} />
              </div>
            )}
          </Tab>

          {/* Offers Tab */}
          <Tab eventKey="offers" title="Offers">
            {isBrand && (
              <div style={{
                padding: 20,
                borderRadius: 'var(--radius-md)',
                border: '1px solid var(--color-border)',
                marginBottom: 20,
              }}>
                <h6 style={{ fontWeight: 600, marginBottom: 12 }}>Send Offer</h6>
                <Form onSubmit={handleSendOffer}>
                  <Row className="g-3">
                    <Col md={4}>
                      <Form.Select
                        value={offerForm.influencer_id}
                        onChange={(e) => setOfferForm((p) => ({ ...p, influencer_id: e.target.value }))}
                        required
                      >
                        <option value="">Select Influencer</option>
                        {invitations
                          .filter((inv) => inv.status === 'interested' || inv.status === 'accepted')
                          .map((inv) => (
                            <option key={inv.influencer_id} value={inv.influencer_id}>
                              {inv.influencer_name || `Influencer #${inv.influencer_id}`}
                            </option>
                          ))}
                      </Form.Select>
                    </Col>
                    <Col md={3}>
                      <Form.Control
                        type="number"
                        placeholder="Amount ($)"
                        value={offerForm.amount}
                        onChange={(e) => setOfferForm((p) => ({ ...p, amount: e.target.value }))}
                        required
                      />
                    </Col>
                    <Col md={3}>
                      <Form.Control
                        placeholder="Message"
                        value={offerForm.message}
                        onChange={(e) => setOfferForm((p) => ({ ...p, message: e.target.value }))}
                      />
                    </Col>
                    <Col md={2}>
                      <Button type="submit" variant="primary" className="w-100">Send</Button>
                    </Col>
                  </Row>
                </Form>
              </div>
            )}

            {offers.length > 0 ? (
              <Row className="g-3">
                {offers.map((offer) => (
                  <Col key={offer.id} md={6}>
                    <OfferCard offer={offer} role={user?.role} />
                  </Col>
                ))}
              </Row>
            ) : (
              <p style={{ color: 'var(--color-text-muted)', textAlign: 'center', padding: 24 }}>
                No offers yet
              </p>
            )}
          </Tab>

          {/* Content Tab */}
          <Tab eventKey="content" title="Content">
            {isInfluencer && (
              <div style={{ marginBottom: 24 }}>
                <FileUpload onFilesSelected={handleContentUpload} />
              </div>
            )}

            {assets.length > 0 ? (
              <Row className="g-3">
                {assets.map((asset) => (
                  <Col key={asset.id} md={4}>
                    <div style={{
                      borderRadius: 'var(--radius-md)',
                      border: '1px solid var(--color-border)',
                      overflow: 'hidden',
                    }}>
                      {asset.file_type?.startsWith('image') ? (
                        <img
                          src={asset.url}
                          alt={asset.filename}
                          style={{ width: '100%', height: 180, objectFit: 'cover' }}
                        />
                      ) : (
                        <div style={{
                          height: 180, display: 'flex', alignItems: 'center',
                          justifyContent: 'center', background: '#f3f4f6',
                          color: 'var(--color-text-muted)', fontSize: 14,
                        }}>
                          {asset.filename || 'File'}
                        </div>
                      )}
                      <div style={{ padding: '12px 16px' }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 }}>
                          <span style={{ fontSize: 13, fontWeight: 500, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', flex: 1 }}>
                            {asset.filename}
                          </span>
                          <StatusBadge status={asset.status || 'pending'} />
                        </div>
                        {(isAdmin || isBrand) && asset.status !== 'content_approved' && (
                          <div style={{ display: 'flex', gap: 8 }}>
                            <Button size="sm" variant="success" onClick={() => handleAssetAction(asset.id, 'approve')}>
                              Approve
                            </Button>
                            <Button size="sm" variant="outline-warning" onClick={() => handleAssetAction(asset.id, 'revision')}>
                              Request Revision
                            </Button>
                          </div>
                        )}
                      </div>
                    </div>
                  </Col>
                ))}
              </Row>
            ) : (
              <p style={{ color: 'var(--color-text-muted)', textAlign: 'center', padding: 24 }}>
                No content uploaded yet
              </p>
            )}
          </Tab>
        </Tabs>
      </div>
    </div>
  );
}
