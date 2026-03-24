import React, { useState, useEffect } from 'react';
import { Row, Col } from 'react-bootstrap';
import { FiTarget, FiCheckCircle, FiClock, FiTrendingUp } from 'react-icons/fi';
import { useAuth } from '../../context/AuthContext';
import api from '../../services/api';
import KPICard from '../../components/KPICard';
import PipelineTracker from '../../components/PipelineTracker';
import CampaignCard from '../../components/CampaignCard';
import LoadingSpinner from '../../components/LoadingSpinner';

export default function DashboardPage() {
  const { user } = useAuth();
  const [stats, setStats] = useState(null);
  const [campaigns, setCampaigns] = useState([]);
  const [pipelineCounts, setPipelineCounts] = useState({});
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboard();
  }, []);

  const fetchDashboard = async () => {
    try {
      const [statsRes, campaignsRes] = await Promise.allSettled([
        api.get('/dashboard/stats'),
        api.get('/campaigns?limit=6'),
      ]);

      if (statsRes.status === 'fulfilled') {
        setStats(statsRes.value.data);
        setPipelineCounts(statsRes.value.data.pipeline || {});
      }
      if (campaignsRes.status === 'fulfilled') {
        setCampaigns(campaignsRes.value.data?.campaigns || campaignsRes.value.data || []);
      }

      try {
        const notifRes = await api.get('/notifications?limit=5');
        setNotifications(notifRes.data || []);
      } catch {
        // notifications not critical
      }
    } catch {
      // handled by interceptor
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <LoadingSpinner />;

  const displayName = user?.first_name || user?.display_name || user?.email?.split('@')[0] || 'User';

  return (
    <div>
      {/* Welcome hero */}
      <div style={{
        background: 'linear-gradient(135deg, var(--color-primary) 0%, #3a5fd9 100%)',
        borderRadius: 'var(--radius-lg)',
        padding: '32px',
        color: '#fff',
        marginBottom: 28,
      }}>
        <h2 style={{ fontWeight: 700, marginBottom: 4, fontSize: 26 }}>
          Welcome back, {displayName}
        </h2>
        <p style={{ opacity: 0.8, fontSize: 14, margin: 0 }}>
          Here's what's happening with your campaigns today.
        </p>
      </div>

      {/* KPIs */}
      <Row className="g-3 mb-4">
        <Col xs={12} sm={6} lg={3}>
          <KPICard
            icon={<FiTarget />}
            label="Active Campaigns"
            value={stats?.active_campaigns ?? 0}
            color="var(--color-primary)"
          />
        </Col>
        <Col xs={12} sm={6} lg={3}>
          <KPICard
            icon={<FiCheckCircle />}
            label="Accepted Collabs"
            value={stats?.accepted_collabs ?? 0}
            color="var(--color-success)"
          />
        </Col>
        <Col xs={12} sm={6} lg={3}>
          <KPICard
            icon={<FiClock />}
            label="Pending Actions"
            value={stats?.pending_actions ?? 0}
            color="var(--color-warning)"
          />
        </Col>
        <Col xs={12} sm={6} lg={3}>
          <KPICard
            icon={<FiTrendingUp />}
            label="Total Campaigns"
            value={stats?.total_campaigns ?? 0}
            color="#8b5cf6"
          />
        </Col>
      </Row>

      {/* Pipeline */}
      <div style={{
        background: 'var(--color-card-bg)',
        borderRadius: 'var(--radius-lg)',
        boxShadow: 'var(--shadow-md)',
        padding: '24px 32px',
        marginBottom: 28,
      }}>
        <h5 style={{ fontWeight: 600, marginBottom: 8 }}>Campaign Pipeline</h5>
        <PipelineTracker counts={pipelineCounts} />
      </div>

      {/* Main content grid */}
      <Row className="g-4">
        <Col lg={8}>
          <h5 style={{ fontWeight: 600, marginBottom: 16 }}>Recent Campaigns</h5>
          {campaigns.length === 0 ? (
            <div style={{
              background: 'var(--color-card-bg)',
              borderRadius: 'var(--radius-lg)',
              boxShadow: 'var(--shadow-md)',
              padding: 48,
              textAlign: 'center',
              color: 'var(--color-text-muted)',
            }}>
              No campaigns yet
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
              {campaigns.map((c) => (
                <CampaignCard key={c.id} campaign={c} />
              ))}
            </div>
          )}
        </Col>
        <Col lg={4}>
          <h5 style={{ fontWeight: 600, marginBottom: 16 }}>Recent Activity</h5>
          <div style={{
            background: 'var(--color-card-bg)',
            borderRadius: 'var(--radius-lg)',
            boxShadow: 'var(--shadow-md)',
            overflow: 'hidden',
          }}>
            {notifications.length === 0 ? (
              <div style={{ padding: 32, textAlign: 'center', color: 'var(--color-text-muted)', fontSize: 14 }}>
                No recent activity
              </div>
            ) : (
              notifications.map((n, i) => (
                <div
                  key={n.id || i}
                  style={{
                    padding: '14px 20px',
                    borderBottom: i < notifications.length - 1 ? '1px solid var(--color-border)' : 'none',
                  }}
                >
                  <div style={{ fontSize: 13, fontWeight: n.read ? 400 : 500 }}>
                    {n.message}
                  </div>
                  <div style={{ fontSize: 11, color: 'var(--color-text-muted)', marginTop: 4 }}>
                    {n.created_at ? new Date(n.created_at).toLocaleDateString() : ''}
                  </div>
                </div>
              ))
            )}
          </div>
        </Col>
      </Row>
    </div>
  );
}
