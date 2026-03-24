import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Form, Button, Row, Col, Alert } from 'react-bootstrap';
import FileUpload from '../../components/FileUpload';
import api from '../../services/api';

const countryOptions = [
  'United States', 'United Kingdom', 'Germany', 'France', 'Spain',
  'Italy', 'Netherlands', 'Sweden', 'Norway', 'Denmark',
  'Brazil', 'Mexico', 'Canada', 'Australia', 'Japan',
  'South Korea', 'India', 'UAE', 'Saudi Arabia', 'South Africa',
];

export default function CampaignCreatePage() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    budget: '',
    target_audience: '',
    countries: [],
    start_date: '',
    end_date: '',
  });
  const [briefingFile, setBriefingFile] = useState(null);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleCountryChange = (e) => {
    const selected = Array.from(e.target.selectedOptions, (opt) => opt.value);
    setFormData((prev) => ({ ...prev, countries: selected }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const payload = new FormData();
      payload.append('title', formData.title);
      payload.append('description', formData.description);
      payload.append('budget', formData.budget);
      payload.append('target_audience', formData.target_audience);
      payload.append('countries', JSON.stringify(formData.countries));
      payload.append('start_date', formData.start_date);
      payload.append('end_date', formData.end_date);
      if (briefingFile) {
        payload.append('briefing', briefingFile);
      }

      const res = await api.post('/campaigns', payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      navigate(`/campaigns/${res.data.id || res.data.campaign?.id}`);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create campaign');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ maxWidth: 720, margin: '0 auto' }}>
      <h3 style={{ fontWeight: 700, marginBottom: 24 }}>Create Campaign</h3>

      <div style={{
        background: 'var(--color-card-bg)',
        borderRadius: 'var(--radius-lg)',
        boxShadow: 'var(--shadow-md)',
        padding: 32,
      }}>
        {error && <Alert variant="danger" style={{ borderRadius: 8 }}>{error}</Alert>}

        <Form onSubmit={handleSubmit}>
          <Form.Group className="mb-3">
            <Form.Label>Campaign Title</Form.Label>
            <Form.Control
              name="title"
              value={formData.title}
              onChange={handleChange}
              placeholder="e.g. Summer Collection Launch"
              required
            />
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>Description</Form.Label>
            <Form.Control
              as="textarea"
              rows={4}
              name="description"
              value={formData.description}
              onChange={handleChange}
              placeholder="Describe your campaign goals, requirements, and expectations..."
              required
            />
          </Form.Group>

          <Row>
            <Col md={4}>
              <Form.Group className="mb-3">
                <Form.Label>Budget ($)</Form.Label>
                <Form.Control
                  type="number"
                  name="budget"
                  value={formData.budget}
                  onChange={handleChange}
                  placeholder="10000"
                  min="0"
                  required
                />
              </Form.Group>
            </Col>
            <Col md={8}>
              <Form.Group className="mb-3">
                <Form.Label>Target Audience</Form.Label>
                <Form.Control
                  name="target_audience"
                  value={formData.target_audience}
                  onChange={handleChange}
                  placeholder="e.g. Women 18-35 interested in fashion"
                />
              </Form.Group>
            </Col>
          </Row>

          <Row>
            <Col md={6}>
              <Form.Group className="mb-3">
                <Form.Label>Start Date</Form.Label>
                <Form.Control
                  type="date"
                  name="start_date"
                  value={formData.start_date}
                  onChange={handleChange}
                  required
                />
              </Form.Group>
            </Col>
            <Col md={6}>
              <Form.Group className="mb-3">
                <Form.Label>End Date</Form.Label>
                <Form.Control
                  type="date"
                  name="end_date"
                  value={formData.end_date}
                  onChange={handleChange}
                  required
                />
              </Form.Group>
            </Col>
          </Row>

          <Form.Group className="mb-3">
            <Form.Label>Target Countries</Form.Label>
            <Form.Select
              multiple
              name="countries"
              value={formData.countries}
              onChange={handleCountryChange}
              style={{ height: 140 }}
            >
              {countryOptions.map((c) => (
                <option key={c} value={c}>{c}</option>
              ))}
            </Form.Select>
            <Form.Text className="text-muted">Hold Ctrl/Cmd to select multiple</Form.Text>
          </Form.Group>

          <Form.Group className="mb-4">
            <Form.Label>Campaign Briefing (PDF)</Form.Label>
            <FileUpload
              onFilesSelected={(files) => setBriefingFile(files[0] || null)}
              accept={{ 'application/pdf': ['.pdf'] }}
              maxFiles={1}
            />
          </Form.Group>

          <div style={{ display: 'flex', gap: 12, justifyContent: 'flex-end' }}>
            <Button variant="outline-primary" onClick={() => navigate('/campaigns')}>
              Cancel
            </Button>
            <Button type="submit" variant="primary" disabled={loading}>
              {loading ? 'Creating...' : 'Create Campaign'}
            </Button>
          </div>
        </Form>
      </div>
    </div>
  );
}
