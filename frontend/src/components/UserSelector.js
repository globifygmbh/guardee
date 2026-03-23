import React, { useState, useEffect } from 'react';
import { Button, Form } from 'react-bootstrap';
import { FiUser, FiCheck } from 'react-icons/fi';
import api from '../services/api';

export default function UserSelector({ onInvite, campaignId }) {
  const [influencers, setInfluencers] = useState([]);
  const [selected, setSelected] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchInfluencers();
  }, []);

  const fetchInfluencers = async () => {
    try {
      const res = await api.get('/users?role=influencer');
      setInfluencers(res.data || []);
    } catch {
      setInfluencers([]);
    }
  };

  const toggleSelect = (id) => {
    setSelected((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
    );
  };

  const handleInvite = async () => {
    if (selected.length === 0) return;
    setLoading(true);
    try {
      await Promise.all(
        selected.map((influencerId) =>
          api.post(`/campaigns/${campaignId}/invite`, { influencer_id: influencerId })
        )
      );
      setSelected([]);
      if (onInvite) onInvite();
    } catch {
      // handle error
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
        <h6 style={{ fontWeight: 600, margin: 0 }}>Select Influencers to Invite</h6>
        <Button
          variant="primary"
          size="sm"
          disabled={selected.length === 0 || loading}
          onClick={handleInvite}
        >
          {loading ? 'Inviting...' : `Invite Selected (${selected.length})`}
        </Button>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(240px, 1fr))', gap: 12 }}>
        {influencers.map((inf) => {
          const isSelected = selected.includes(inf.id);
          return (
            <div
              key={inf.id}
              onClick={() => toggleSelect(inf.id)}
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: 12,
                padding: '14px 16px',
                borderRadius: 'var(--radius-md)',
                border: `2px solid ${isSelected ? 'var(--color-primary)' : 'var(--color-border)'}`,
                cursor: 'pointer',
                transition: 'all 0.2s',
                background: isSelected ? 'rgba(37, 76, 203, 0.04)' : 'var(--color-card-bg)',
              }}
            >
              <div style={{ position: 'relative' }}>
                <div style={{
                  width: 40,
                  height: 40,
                  borderRadius: '50%',
                  background: '#f3f4f6',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: 'var(--color-text-muted)',
                }}>
                  <FiUser size={18} />
                </div>
                {isSelected && (
                  <div style={{
                    position: 'absolute',
                    bottom: -2,
                    right: -2,
                    width: 18,
                    height: 18,
                    borderRadius: '50%',
                    background: 'var(--color-primary)',
                    color: '#fff',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                  }}>
                    <FiCheck size={10} />
                  </div>
                )}
              </div>
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontWeight: 600, fontSize: 14 }}>
                  {inf.display_name || `${inf.first_name} ${inf.last_name}`}
                </div>
                <div style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>
                  {inf.niche && <span>{inf.niche}</span>}
                  {inf.instagram_handle && <span> @{inf.instagram_handle}</span>}
                </div>
              </div>
              <Form.Check
                type="checkbox"
                checked={isSelected}
                onChange={() => toggleSelect(inf.id)}
                onClick={(e) => e.stopPropagation()}
              />
            </div>
          );
        })}
      </div>

      {influencers.length === 0 && (
        <div style={{ textAlign: 'center', color: 'var(--color-text-muted)', padding: 32 }}>
          No influencers found
        </div>
      )}
    </div>
  );
}
