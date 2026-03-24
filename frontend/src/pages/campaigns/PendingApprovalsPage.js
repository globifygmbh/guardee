import React, { useState, useEffect } from 'react';
import { Button, Alert } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge';
import LoadingSpinner from '../../components/LoadingSpinner';

export default function PendingApprovalsPage() {
  const navigate = useNavigate();
  const [campaigns, setCampaigns] = useState([]);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState({});
  const [success, setSuccess] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    fetchPending();
  }, []);

  const fetchPending = async () => {
    try {
      const res = await api.get('/campaigns', { params: { status: 'pending_approval' } });
      setCampaigns(res.data?.campaigns || res.data || []);
    } catch {
      setCampaigns([]);
    } finally {
      setLoading(false);
    }
  };

  const handleAction = async (id, action) => {
    setActionLoading((prev) => ({ ...prev, [id]: true }));
    setError('');
    try {
      await api.put(`/campaigns/${id}/${action}`);
      setSuccess(`Campaign ${action}d successfully`);
      setCampaigns((prev) => prev.filter((c) => c.id !== id));
    } catch (err) {
      setError(err.response?.data?.message || `Failed to ${action} campaign`);
    } finally {
      setActionLoading((prev) => ({ ...prev, [id]: false }));
    }
  };

  if (loading) return <LoadingSpinner />;

  return (
    <div>
      <h3 style={{ fontWeight: 700, marginBottom: 24 }}>Pending Approvals</h3>

      {success && <Alert variant="success" dismissible onClose={() => setSuccess('')} style={{ borderRadius: 8 }}>{success}</Alert>}
      {error && <Alert variant="danger" dismissible onClose={() => setError('')} style={{ borderRadius: 8 }}>{error}</Alert>}

      {campaigns.length === 0 ? (
        <div style={{
          background: 'var(--color-card-bg)',
          borderRadius: 'var(--radius-lg)',
          boxShadow: 'var(--shadow-md)',
          padding: 64,
          textAlign: 'center',
          color: 'var(--color-text-muted)',
        }}>
          No campaigns pending approval
        </div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
          {campaigns.map((campaign) => (
            <div
              key={campaign.id}
              style={{
                background: 'var(--color-card-bg)',
                borderRadius: 'var(--radius-lg)',
                boxShadow: 'var(--shadow-md)',
                padding: '20px 24px',
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                flexWrap: 'wrap',
                gap: 16,
              }}
            >
              <div
                style={{ cursor: 'pointer', flex: 1 }}
                onClick={() => navigate(`/campaigns/${campaign.id}`)}
              >
                <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 4 }}>
                  <h6 style={{ fontWeight: 600, margin: 0 }}>{campaign.title}</h6>
                  <StatusBadge status={campaign.status} />
                </div>
                <div style={{ fontSize: 13, color: 'var(--color-text-muted)' }}>
                  {campaign.description?.substring(0, 100)}{campaign.description?.length > 100 ? '...' : ''}
                  {campaign.budget && <span style={{ marginLeft: 12 }}>Budget: ${Number(campaign.budget).toLocaleString()}</span>}
                </div>
              </div>
              <div style={{ display: 'flex', gap: 8, flexShrink: 0 }}>
                <Button
                  variant="success"
                  size="sm"
                  onClick={() => handleAction(campaign.id, 'approve')}
                  disabled={actionLoading[campaign.id]}
                >
                  Approve
                </Button>
                <Button
                  variant="outline-danger"
                  size="sm"
                  onClick={() => handleAction(campaign.id, 'reject')}
                  disabled={actionLoading[campaign.id]}
                >
                  Reject
                </Button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
