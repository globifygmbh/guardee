import React, { useState, useEffect } from 'react';
import { Row, Col, Form, Button } from 'react-bootstrap';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { FiPlus } from 'react-icons/fi';
import { useAuth } from '../../context/AuthContext';
import api from '../../services/api';
import CampaignCard from '../../components/CampaignCard';
import LoadingSpinner from '../../components/LoadingSpinner';

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'draft', label: 'Draft' },
  { value: 'pending_approval', label: 'Pending Approval' },
  { value: 'approved', label: 'Approved' },
  { value: 'active', label: 'Active' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' },
];

export default function CampaignListPage() {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const [campaigns, setCampaigns] = useState([]);
  const [statusFilter, setStatusFilter] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchCampaigns();
  }, [statusFilter]);

  const fetchCampaigns = async () => {
    setLoading(true);
    try {
      const params = {};
      if (statusFilter) params.status = statusFilter;
      if (searchParams.get('mine') === 'true') params.mine = true;

      const res = await api.get('/campaigns', { params });
      setCampaigns(res.data?.campaigns || res.data || []);
    } catch {
      setCampaigns([]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <div style={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 24,
        flexWrap: 'wrap',
        gap: 12,
      }}>
        <h3 style={{ fontWeight: 700, margin: 0 }}>Campaigns</h3>
        <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
          <Form.Select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            style={{ width: 180, fontSize: 14 }}
          >
            {statusOptions.map((opt) => (
              <option key={opt.value} value={opt.value}>{opt.label}</option>
            ))}
          </Form.Select>
          {(user?.role === 'brand' || user?.role === 'admin') && (
            <Button variant="primary" onClick={() => navigate('/campaigns/create')}>
              <FiPlus size={16} style={{ marginRight: 6 }} />
              New Campaign
            </Button>
          )}
        </div>
      </div>

      {loading ? (
        <LoadingSpinner />
      ) : campaigns.length === 0 ? (
        <div style={{
          background: 'var(--color-card-bg)',
          borderRadius: 'var(--radius-lg)',
          boxShadow: 'var(--shadow-md)',
          padding: 64,
          textAlign: 'center',
        }}>
          <p style={{ color: 'var(--color-text-muted)', fontSize: 16, marginBottom: 16 }}>
            No campaigns found
          </p>
          {(user?.role === 'brand' || user?.role === 'admin') && (
            <Button variant="primary" onClick={() => navigate('/campaigns/create')}>
              Create Your First Campaign
            </Button>
          )}
        </div>
      ) : (
        <Row className="g-3">
          {campaigns.map((campaign) => (
            <Col key={campaign.id} xs={12} md={6} xl={4}>
              <CampaignCard campaign={campaign} />
            </Col>
          ))}
        </Row>
      )}
    </div>
  );
}
