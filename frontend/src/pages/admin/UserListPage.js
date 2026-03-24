import React, { useState, useEffect } from 'react';
import { Card, Row, Col, Spinner, Alert, Nav, Badge } from 'react-bootstrap';
import { FiUser, FiInstagram, FiBriefcase, FiGlobe, FiUsers } from 'react-icons/fi';
import api from '../../services/api';

export default function UserListPage() {
  const [influencers, setInfluencers] = useState([]);
  const [brands, setBrands] = useState([]);
  const [activeTab, setActiveTab] = useState('influencers');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [infRes, brandRes] = await Promise.all([
          api.get('/users/influencers'),
          api.get('/users/brands'),
        ]);
        setInfluencers(infRes.data.data || []);
        setBrands(brandRes.data.data || []);
      } catch (err) {
        setError('Failed to load users');
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  if (loading) return <div className="text-center py-5"><Spinner animation="border" /></div>;

  return (
    <div>
      <div style={{ marginBottom: 24 }}>
        <h4 style={{ fontWeight: 700 }}>User Management</h4>
        <p style={{ color: 'var(--color-text-muted)', fontSize: 14 }}>
          Manage influencers and brands on the platform
        </p>
      </div>

      {error && <Alert variant="danger" dismissible onClose={() => setError(null)}>{error}</Alert>}

      <Card className="card" style={{ overflow: 'hidden' }}>
        <Nav variant="tabs" activeKey={activeTab} onSelect={setActiveTab} style={{ borderBottom: '1px solid var(--color-border)' }}>
          <Nav.Item>
            <Nav.Link eventKey="influencers" style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '14px 24px', fontWeight: 500, fontSize: 14 }}>
              <FiUsers size={16} /> Influencers
              <Badge pill bg="secondary" style={{ fontSize: 11 }}>{influencers.length}</Badge>
            </Nav.Link>
          </Nav.Item>
          <Nav.Item>
            <Nav.Link eventKey="brands" style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '14px 24px', fontWeight: 500, fontSize: 14 }}>
              <FiBriefcase size={16} /> Brands
              <Badge pill bg="secondary" style={{ fontSize: 11 }}>{brands.length}</Badge>
            </Nav.Link>
          </Nav.Item>
        </Nav>

        <div style={{ padding: 24 }}>
          {activeTab === 'influencers' && (
            <Row className="g-3">
              {influencers.length === 0 ? (
                <Col xs={12}><p style={{ color: 'var(--color-text-muted)', textAlign: 'center', padding: 32 }}>No influencers registered yet.</p></Col>
              ) : (
                influencers.map((inf) => (
                  <Col key={inf.id} xs={12} md={6} xl={4}>
                    <div style={{
                      border: '1px solid var(--color-border)',
                      borderRadius: 12,
                      padding: 20,
                      display: 'flex',
                      gap: 16,
                      alignItems: 'flex-start',
                      transition: 'box-shadow 0.2s',
                    }}>
                      <div style={{
                        width: 48, height: 48, borderRadius: '50%',
                        background: 'var(--color-primary)', color: '#fff',
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                        fontWeight: 700, fontSize: 18, flexShrink: 0,
                      }}>
                        {inf.display_name?.[0]?.toUpperCase() || <FiUser />}
                      </div>
                      <div style={{ flex: 1, minWidth: 0 }}>
                        <h6 style={{ fontWeight: 600, marginBottom: 2 }}>{inf.display_name || 'Unknown'}</h6>
                        {inf.niche && (
                          <Badge pill bg="light" text="dark" style={{ fontSize: 11, fontWeight: 500, marginBottom: 8 }}>
                            {inf.niche}
                          </Badge>
                        )}
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, fontSize: 13, color: 'var(--color-text-muted)' }}>
                          {inf.instagram_handle && (
                            <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                              <FiInstagram size={13} /> {inf.instagram_handle}
                            </span>
                          )}
                          {inf.country && (
                            <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                              <FiGlobe size={13} /> {inf.country}
                            </span>
                          )}
                          {inf.followers_count > 0 && (
                            <span style={{ fontWeight: 600, color: 'var(--color-text)' }}>
                              {inf.followers_count >= 1000000
                                ? `${(inf.followers_count / 1000000).toFixed(1)}M`
                                : inf.followers_count >= 1000
                                  ? `${(inf.followers_count / 1000).toFixed(0)}K`
                                  : inf.followers_count
                              } followers
                            </span>
                          )}
                        </div>
                      </div>
                    </div>
                  </Col>
                ))
              )}
            </Row>
          )}

          {activeTab === 'brands' && (
            <Row className="g-3">
              {brands.length === 0 ? (
                <Col xs={12}><p style={{ color: 'var(--color-text-muted)', textAlign: 'center', padding: 32 }}>No brands registered yet.</p></Col>
              ) : (
                brands.map((brand) => (
                  <Col key={brand.id} xs={12} md={6} xl={4}>
                    <div style={{
                      border: '1px solid var(--color-border)',
                      borderRadius: 12,
                      padding: 20,
                      display: 'flex',
                      gap: 16,
                      alignItems: 'flex-start',
                    }}>
                      <div style={{
                        width: 48, height: 48, borderRadius: 12,
                        background: '#f3f4f6',
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                        flexShrink: 0,
                      }}>
                        <FiBriefcase size={20} color="var(--color-text-muted)" />
                      </div>
                      <div style={{ flex: 1, minWidth: 0 }}>
                        <h6 style={{ fontWeight: 600, marginBottom: 2 }}>{brand.company_name}</h6>
                        {brand.industry && (
                          <Badge pill bg="light" text="dark" style={{ fontSize: 11, fontWeight: 500, marginBottom: 8 }}>
                            {brand.industry}
                          </Badge>
                        )}
                        {brand.website && (
                          <p style={{ fontSize: 12, color: 'var(--color-primary)', marginBottom: 0 }}>
                            {brand.website}
                          </p>
                        )}
                      </div>
                    </div>
                  </Col>
                ))
              )}
            </Row>
          )}
        </div>
      </Card>
    </div>
  );
}
