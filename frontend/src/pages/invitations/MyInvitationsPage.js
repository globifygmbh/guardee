import React, { useState, useEffect } from 'react';
import { Card, Row, Col, Button, Spinner, Alert } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge';

export default function MyInvitationsPage() {
  const [invitations, setInvitations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const navigate = useNavigate();

  const fetchInvitations = async () => {
    try {
      const res = await api.get('/invitations/mine');
      setInvitations(res.data.data || []);
    } catch (err) {
      setError('Failed to load invitations');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchInvitations(); }, []);

  const handleRespond = async (id, status) => {
    try {
      await api.post(`/invitations/${id}/respond`, { status });
      fetchInvitations();
    } catch (err) {
      setError('Failed to respond to invitation');
    }
  };

  if (loading) return <div className="text-center py-5"><Spinner animation="border" /></div>;

  return (
    <div>
      <div style={{ marginBottom: 24 }}>
        <h4 style={{ fontWeight: 700 }}>My Invitations</h4>
        <p style={{ color: 'var(--color-text-muted)', fontSize: 14 }}>
          Campaign invitations you've received
        </p>
      </div>

      {error && <Alert variant="danger" dismissible onClose={() => setError(null)}>{error}</Alert>}

      {invitations.length === 0 ? (
        <Card className="card" style={{ padding: 48, textAlign: 'center' }}>
          <p style={{ color: 'var(--color-text-muted)', marginBottom: 0 }}>
            No invitations yet. You'll be notified when brands invite you to campaigns.
          </p>
        </Card>
      ) : (
        <Row className="g-3">
          {invitations.map((inv) => (
            <Col key={inv.id} xs={12}>
              <Card className="card" style={{ padding: 24 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: 16 }}>
                  <div style={{ flex: 1, minWidth: 200 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 8 }}>
                      <h5 style={{ fontWeight: 600, marginBottom: 0, cursor: 'pointer' }}
                        onClick={() => navigate(`/campaigns/${inv.campaign_id}`)}>
                        {inv.campaign_title || `Campaign #${inv.campaign_id}`}
                      </h5>
                      <StatusBadge status={inv.status} />
                    </div>
                    <div style={{ display: 'flex', gap: 24, fontSize: 13, color: 'var(--color-text-muted)' }}>
                      {inv.campaign_budget && (
                        <span>Budget: <strong style={{ color: 'var(--color-text)' }}>{Number(inv.campaign_budget).toLocaleString('de-DE')} EUR</strong></span>
                      )}
                      {inv.campaign_start_date && (
                        <span>{inv.campaign_start_date} - {inv.campaign_end_date}</span>
                      )}
                    </div>
                    {inv.notes && (
                      <p style={{ marginTop: 8, fontSize: 13, color: 'var(--color-text-muted)' }}>{inv.notes}</p>
                    )}
                  </div>

                  {inv.status === 'pending' && (
                    <div style={{ display: 'flex', gap: 8 }}>
                      <Button
                        variant="success"
                        size="sm"
                        onClick={() => handleRespond(inv.id, 'interested')}
                        style={{ borderRadius: 8, padding: '8px 20px' }}
                      >
                        I'm Interested
                      </Button>
                      <Button
                        variant="outline-danger"
                        size="sm"
                        onClick={() => handleRespond(inv.id, 'declined')}
                        style={{ borderRadius: 8, padding: '8px 20px' }}
                      >
                        Decline
                      </Button>
                    </div>
                  )}

                  {inv.status === 'interested' && inv.offer_id && (
                    <Button
                      variant="primary"
                      size="sm"
                      onClick={() => navigate(`/offers/${inv.offer_id}`)}
                      style={{ borderRadius: 8, padding: '8px 20px' }}
                    >
                      View Offer
                    </Button>
                  )}
                </div>
              </Card>
            </Col>
          ))}
        </Row>
      )}
    </div>
  );
}
