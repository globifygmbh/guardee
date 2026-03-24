import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Card, Button, Spinner, Alert, Form } from 'react-bootstrap';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge';
import { useAuth } from '../../context/AuthContext';

export default function OfferViewPage() {
  const { id } = useParams();
  const { user } = useAuth();
  const navigate = useNavigate();
  const [offer, setOffer] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [updating, setUpdating] = useState(false);
  const [editAmount, setEditAmount] = useState('');
  const [editMessage, setEditMessage] = useState('');
  const [isEditing, setIsEditing] = useState(false);
  const [termsAccepted, setTermsAccepted] = useState(false);

  const fetchOffer = async () => {
    try {
      const res = await api.get(`/invitations/${id}/offer`);
      setOffer(res.data.data);
      setEditAmount(res.data.data?.amount || '');
      setEditMessage(res.data.data?.message || '');
    } catch (err) {
      setError('Failed to load offer details');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchOffer(); }, [id]);

  const handleAccept = async () => {
    setUpdating(true);
    try {
      await api.post(`/offers/${offer.id}/accept`);
      fetchOffer();
    } catch (err) {
      setError('Failed to accept offer');
    } finally {
      setUpdating(false);
    }
  };

  const handleDecline = async () => {
    setUpdating(true);
    try {
      await api.post(`/offers/${offer.id}/decline`);
      fetchOffer();
    } catch (err) {
      setError('Failed to decline offer');
    } finally {
      setUpdating(false);
    }
  };

  const handleUpdate = async () => {
    setUpdating(true);
    try {
      await api.put(`/offers/${offer.id}`, { amount: editAmount, message: editMessage });
      setIsEditing(false);
      fetchOffer();
    } catch (err) {
      setError('Failed to update offer');
    } finally {
      setUpdating(false);
    }
  };

  const handleAcceptTerms = async () => {
    setUpdating(true);
    try {
      await api.post(`/offers/${offer.id}/accept-terms`);
      fetchOffer();
    } catch (err) {
      setError('Failed to accept terms');
    } finally {
      setUpdating(false);
    }
  };

  if (loading) return <div className="text-center py-5"><Spinner animation="border" /></div>;

  if (!offer) {
    return (
      <Card className="card" style={{ padding: 48, textAlign: 'center' }}>
        <p style={{ color: 'var(--color-text-muted)' }}>No offer found for this invitation.</p>
        <Button variant="outline-primary" onClick={() => navigate(-1)}>Go Back</Button>
      </Card>
    );
  }

  const isBrand = user?.role === 'brand';
  const isInfluencer = user?.role === 'influencer';

  return (
    <div style={{ maxWidth: 700, margin: '0 auto' }}>
      <div style={{ marginBottom: 24 }}>
        <Button variant="link" onClick={() => navigate(-1)} style={{ padding: 0, color: 'var(--color-text-muted)', fontSize: 14, marginBottom: 8 }}>
          &larr; Back
        </Button>
        <h4 style={{ fontWeight: 700 }}>Offer Details</h4>
      </div>

      {error && <Alert variant="danger" dismissible onClose={() => setError(null)}>{error}</Alert>}

      <Card className="card" style={{ padding: 32 }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
          <div>
            <p style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 4 }}>Campaign</p>
            <h5 style={{ fontWeight: 600, cursor: 'pointer' }} onClick={() => navigate(`/campaigns/${offer.campaign_id}`)}>
              {offer.campaign_title || `Campaign #${offer.campaign_id}`}
            </h5>
          </div>
          <StatusBadge status={offer.status} />
        </div>

        <div style={{ background: '#f9fafb', borderRadius: 12, padding: 24, marginBottom: 24 }}>
          <div style={{ textAlign: 'center' }}>
            <p style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 4 }}>Offer Amount</p>
            <h2 style={{ fontWeight: 800, color: 'var(--color-primary)', marginBottom: 4 }}>
              {Number(offer.amount).toLocaleString('de-DE')} {offer.currency}
            </h2>
          </div>
        </div>

        {offer.message && (
          <div style={{ marginBottom: 24 }}>
            <p style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 4 }}>Message</p>
            <p style={{ fontSize: 14 }}>{offer.message}</p>
          </div>
        )}

        {/* Brand: Edit offer */}
        {isBrand && offer.status === 'pending' && !isEditing && (
          <Button variant="outline-primary" onClick={() => setIsEditing(true)} className="w-100 mb-3">
            Edit Offer
          </Button>
        )}

        {isEditing && (
          <div style={{ marginBottom: 24 }}>
            <Form.Group className="mb-3">
              <Form.Label>Amount (EUR)</Form.Label>
              <Form.Control type="number" value={editAmount} onChange={(e) => setEditAmount(e.target.value)} />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Message</Form.Label>
              <Form.Control as="textarea" rows={3} value={editMessage} onChange={(e) => setEditMessage(e.target.value)} />
            </Form.Group>
            <div style={{ display: 'flex', gap: 8 }}>
              <Button variant="primary" onClick={handleUpdate} disabled={updating}>
                {updating ? <Spinner size="sm" /> : 'Update Offer'}
              </Button>
              <Button variant="outline-secondary" onClick={() => setIsEditing(false)}>Cancel</Button>
            </div>
          </div>
        )}

        {/* Influencer: Accept / Decline */}
        {isInfluencer && offer.status === 'pending' && (
          <div style={{ display: 'flex', gap: 12 }}>
            <Button variant="success" className="flex-fill" onClick={handleAccept} disabled={updating}>
              {updating ? <Spinner size="sm" /> : 'Accept Offer'}
            </Button>
            <Button variant="outline-danger" className="flex-fill" onClick={handleDecline} disabled={updating}>
              Decline
            </Button>
          </div>
        )}

        {/* Influencer: Accept Terms (AGB) */}
        {isInfluencer && offer.status === 'accepted' && !offer.terms_accepted && (
          <div style={{ marginTop: 16, padding: 20, border: '2px solid var(--color-primary)', borderRadius: 12 }}>
            <h6 style={{ fontWeight: 600, marginBottom: 12 }}>Terms & Conditions</h6>
            <p style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 16 }}>
              By accepting, you agree to the platform terms and conditions for this collaboration.
              Please review the campaign briefing and ensure you understand all deliverables.
            </p>
            <Form.Check
              type="checkbox"
              label="I accept the Terms & Conditions (AGB)"
              checked={termsAccepted}
              onChange={(e) => setTermsAccepted(e.target.checked)}
              style={{ marginBottom: 16, fontSize: 14 }}
            />
            <Button variant="primary" onClick={handleAcceptTerms} disabled={!termsAccepted || updating} className="w-100">
              {updating ? <Spinner size="sm" /> : 'Confirm & Accept Terms'}
            </Button>
          </div>
        )}

        {offer.terms_accepted === 1 && (
          <Alert variant="success" style={{ marginTop: 16, borderRadius: 12 }}>
            Terms accepted. You can now proceed with content creation.
          </Alert>
        )}
      </Card>
    </div>
  );
}
