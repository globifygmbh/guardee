import React from 'react';
import { useNavigate } from 'react-router-dom';
import { FiCalendar, FiUsers, FiDollarSign } from 'react-icons/fi';
import StatusBadge from './StatusBadge';

export default function CampaignCard({ campaign }) {
  const navigate = useNavigate();

  const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  };

  return (
    <div
      onClick={() => navigate(`/campaigns/${campaign.id}`)}
      style={{
        background: 'var(--color-card-bg)',
        borderRadius: 'var(--radius-lg)',
        boxShadow: 'var(--shadow-md)',
        padding: '24px',
        cursor: 'pointer',
        transition: 'all 0.2s ease',
        border: '1px solid transparent',
      }}
      onMouseEnter={(e) => {
        e.currentTarget.style.transform = 'translateY(-2px)';
        e.currentTarget.style.boxShadow = 'var(--shadow-lg)';
        e.currentTarget.style.borderColor = 'var(--color-primary)';
      }}
      onMouseLeave={(e) => {
        e.currentTarget.style.transform = 'translateY(0)';
        e.currentTarget.style.boxShadow = 'var(--shadow-md)';
        e.currentTarget.style.borderColor = 'transparent';
      }}
    >
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 16 }}>
        <h5 style={{ fontWeight: 600, fontSize: '16px', margin: 0, flex: 1, marginRight: 12 }}>
          {campaign.title}
        </h5>
        <StatusBadge status={campaign.status} />
      </div>

      {campaign.description && (
        <p style={{
          fontSize: '13px',
          color: 'var(--color-text-muted)',
          marginBottom: 16,
          lineHeight: 1.5,
          display: '-webkit-box',
          WebkitLineClamp: 2,
          WebkitBoxOrient: 'vertical',
          overflow: 'hidden',
        }}>
          {campaign.description}
        </p>
      )}

      <div style={{ display: 'flex', gap: '16px', flexWrap: 'wrap' }}>
        {campaign.budget && (
          <div style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: '13px', color: 'var(--color-text-muted)' }}>
            <FiDollarSign size={14} />
            <span style={{ fontWeight: 600, color: 'var(--color-text)' }}>
              {Number(campaign.budget).toLocaleString()}
            </span>
          </div>
        )}
        {(campaign.start_date || campaign.end_date) && (
          <div style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: '13px', color: 'var(--color-text-muted)' }}>
            <FiCalendar size={14} />
            {formatDate(campaign.start_date)} - {formatDate(campaign.end_date)}
          </div>
        )}
        {campaign.influencer_count !== undefined && (
          <div style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: '13px', color: 'var(--color-text-muted)' }}>
            <FiUsers size={14} />
            {campaign.influencer_count} influencers
          </div>
        )}
      </div>
    </div>
  );
}
