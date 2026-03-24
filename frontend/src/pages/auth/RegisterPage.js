import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { Form, Button, Alert, Row, Col } from 'react-bootstrap';
import { useAuth } from '../../context/AuthContext';

export default function RegisterPage() {
  const [role, setRole] = useState('');
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    first_name: '',
    last_name: '',
    company_name: '',
    display_name: '',
    niche: '',
    instagram_handle: '',
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { register } = useAuth();
  const navigate = useNavigate();

  const handleChange = (e) => {
    setFormData((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const data = { ...formData, role };
      await register(data);
      navigate('/dashboard');
    } catch (err) {
      setError(err.response?.data?.message || 'Registration failed');
    } finally {
      setLoading(false);
    }
  };

  if (!role) {
    return (
      <div>
        <h4 style={{ fontWeight: 700, marginBottom: 4 }}>Create an account</h4>
        <p style={{ color: 'var(--color-text-muted)', fontSize: 14, marginBottom: 24 }}>
          Choose your account type
        </p>

        <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
          {[
            { value: 'brand', label: 'Brand', desc: 'Launch and manage influencer campaigns' },
            { value: 'influencer', label: 'Influencer', desc: 'Discover campaigns and collaborate with brands' },
          ].map((opt) => (
            <button
              key={opt.value}
              onClick={() => setRole(opt.value)}
              style={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'flex-start',
                padding: '20px',
                borderRadius: 'var(--radius-md)',
                border: '2px solid var(--color-border)',
                background: 'transparent',
                cursor: 'pointer',
                textAlign: 'left',
                transition: 'all 0.2s',
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.borderColor = 'var(--color-primary)';
                e.currentTarget.style.background = 'rgba(37, 76, 203, 0.04)';
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.borderColor = 'var(--color-border)';
                e.currentTarget.style.background = 'transparent';
              }}
            >
              <span style={{ fontWeight: 600, fontSize: 16, marginBottom: 4 }}>{opt.label}</span>
              <span style={{ fontSize: 13, color: 'var(--color-text-muted)' }}>{opt.desc}</span>
            </button>
          ))}
        </div>

        <p style={{ textAlign: 'center', marginTop: 20, fontSize: 14, color: 'var(--color-text-muted)' }}>
          Already have an account?{' '}
          <Link to="/login" style={{ color: 'var(--color-primary)', fontWeight: 600 }}>
            Sign in
          </Link>
        </p>
      </div>
    );
  }

  return (
    <div>
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 20 }}>
        <button
          onClick={() => setRole('')}
          style={{
            background: 'none', border: 'none', cursor: 'pointer',
            color: 'var(--color-text-muted)', fontSize: 14, padding: 0,
          }}
        >
          Back
        </button>
        <span style={{ color: 'var(--color-text-muted)', fontSize: 14 }}>/</span>
        <span style={{ fontSize: 14, fontWeight: 600, textTransform: 'capitalize' }}>{role}</span>
      </div>

      <h4 style={{ fontWeight: 700, marginBottom: 24 }}>Complete your profile</h4>

      {error && <Alert variant="danger" style={{ fontSize: 14, borderRadius: 8 }}>{error}</Alert>}

      <Form onSubmit={handleSubmit}>
        <Row>
          <Col xs={6}>
            <Form.Group className="mb-3">
              <Form.Label>First Name</Form.Label>
              <Form.Control
                name="first_name"
                value={formData.first_name}
                onChange={handleChange}
                placeholder="John"
                required
              />
            </Form.Group>
          </Col>
          <Col xs={6}>
            <Form.Group className="mb-3">
              <Form.Label>Last Name</Form.Label>
              <Form.Control
                name="last_name"
                value={formData.last_name}
                onChange={handleChange}
                placeholder="Doe"
                required
              />
            </Form.Group>
          </Col>
        </Row>

        <Form.Group className="mb-3">
          <Form.Label>Email</Form.Label>
          <Form.Control
            type="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
            placeholder="you@example.com"
            required
          />
        </Form.Group>

        <Form.Group className="mb-3">
          <Form.Label>Password</Form.Label>
          <Form.Control
            type="password"
            name="password"
            value={formData.password}
            onChange={handleChange}
            placeholder="Min 8 characters"
            required
            minLength={8}
          />
        </Form.Group>

        {role === 'brand' && (
          <Form.Group className="mb-3">
            <Form.Label>Company Name</Form.Label>
            <Form.Control
              name="company_name"
              value={formData.company_name}
              onChange={handleChange}
              placeholder="Your company"
              required
            />
          </Form.Group>
        )}

        {role === 'influencer' && (
          <>
            <Form.Group className="mb-3">
              <Form.Label>Display Name</Form.Label>
              <Form.Control
                name="display_name"
                value={formData.display_name}
                onChange={handleChange}
                placeholder="Your public name"
                required
              />
            </Form.Group>

            <Row>
              <Col xs={6}>
                <Form.Group className="mb-3">
                  <Form.Label>Niche</Form.Label>
                  <Form.Select name="niche" value={formData.niche} onChange={handleChange} required>
                    <option value="">Select...</option>
                    <option value="fashion">Fashion</option>
                    <option value="beauty">Beauty</option>
                    <option value="fitness">Fitness</option>
                    <option value="food">Food</option>
                    <option value="travel">Travel</option>
                    <option value="tech">Tech</option>
                    <option value="lifestyle">Lifestyle</option>
                    <option value="gaming">Gaming</option>
                    <option value="other">Other</option>
                  </Form.Select>
                </Form.Group>
              </Col>
              <Col xs={6}>
                <Form.Group className="mb-3">
                  <Form.Label>Instagram Handle</Form.Label>
                  <Form.Control
                    name="instagram_handle"
                    value={formData.instagram_handle}
                    onChange={handleChange}
                    placeholder="@username"
                  />
                </Form.Group>
              </Col>
            </Row>
          </>
        )}

        <Button type="submit" variant="primary" className="w-100" disabled={loading}
          style={{ padding: '12px', fontWeight: 600, marginTop: 8 }}>
          {loading ? 'Creating account...' : 'Create Account'}
        </Button>
      </Form>

      <p style={{ textAlign: 'center', marginTop: 20, fontSize: 14, color: 'var(--color-text-muted)' }}>
        Already have an account?{' '}
        <Link to="/login" style={{ color: 'var(--color-primary)', fontWeight: 600 }}>
          Sign in
        </Link>
      </p>
    </div>
  );
}
