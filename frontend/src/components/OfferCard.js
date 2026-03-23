import React from 'react';
import { useNavigate } from 'react-router-dom';
import { Button } from 'react-bootstrap';
import { FiDollarSign, FiUser } from 'react-icons/fi';
import StatusBadge from './StatusBadge';

export default function OfferCard({ offer, role, onAction }) {
  const navigate = useNavigate();

  return (
    <div style={{
      background: 'var(--color-card-bg)',
      borderRadius: 'var(--radius-lg)',
      boxShadow: 'var(--shadow-sm)',
      padding: '20px',
      border: '1px solid var(--color-border)',
    }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
          <FiUser size={16} />
          <span style={{ fontWeight: 600, fontSize: 14 }}>
            {offer.influencer_name || offer.brand_name || 'Unknown'}
          </span>
        </div>
        <StatusBadge status={offer.status} />
      </div>

      <div style={{ display: 'flex', alignItems: 'center', gap: 6, marginBottom: 16, fontSize: 14 }}>
        <FiDollarSign size={16} color="var(--color-success)" />
        <span style={{ fontWeight: 700, fontSize: 20 }}>
          {Number(offer.amount).toLocaleString()}
        </span>
      </div>

      {offer.message && (
        <p style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 16 }}>
          {offer.message}
        </p>
      )}

      <div style={{ display: 'flex', gap: 8 }}>
        <Button
          size="sm"
          variant="outline-primary"
          onClick={() => navigate(`/offers/${offer.id}`)}
        >
          View Details
        </Button>
        {role === 'influencer' && offer.status === 'pending' && (
          <>
            <Button size="sm" variant="success" onClick={() => onAction && onAction('accept', offer.id)}>
              Accept
            </Button>
            <Button size="sm" variant="outline-danger" onClick={() => onAction && onAction('decline', offer.id)}>
              Decline
            </Button>
          </>
        )}
      </div>
    </div>
  );
}
