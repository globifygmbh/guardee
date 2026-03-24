import React, { useState } from 'react';
import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { Dropdown } from 'react-bootstrap';
import {
  FiGrid, FiTarget, FiPlusCircle, FiClock, FiUsers,
  FiMail, FiUpload, FiLogOut, FiSearch, FiChevronDown, FiMenu, FiX
} from 'react-icons/fi';
import { useAuth } from '../context/AuthContext';
import NotificationBell from '../components/NotificationBell';

const navItems = {
  all: [
    { to: '/dashboard', icon: <FiGrid />, label: 'Dashboard' },
    { to: '/campaigns', icon: <FiTarget />, label: 'Campaigns' },
  ],
  admin: [
    { to: '/campaigns/pending', icon: <FiClock />, label: 'Pending Approvals' },
    { to: '/admin/users', icon: <FiUsers />, label: 'Users' },
  ],
  brand: [
    { to: '/campaigns?mine=true', icon: <FiTarget />, label: 'My Campaigns' },
    { to: '/campaigns/create', icon: <FiPlusCircle />, label: 'Create Campaign' },
  ],
  influencer: [
    { to: '/invitations', icon: <FiMail />, label: 'My Invitations' },
    { to: '/upload', icon: <FiUpload />, label: 'My Uploads' },
  ],
};

export default function AppLayout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const roleItems = navItems[user?.role] || [];
  const allNavItems = [...navItems.all, ...roleItems];

  const linkStyle = (isActive) => ({
    display: 'flex',
    alignItems: 'center',
    gap: 12,
    padding: '10px 16px',
    borderRadius: 8,
    fontSize: 14,
    fontWeight: 500,
    color: isActive ? '#fff' : 'rgba(255,255,255,0.6)',
    backgroundColor: isActive ? 'var(--color-primary)' : 'transparent',
    transition: 'all 0.2s',
    textDecoration: 'none',
  });

  return (
    <div style={{ display: 'flex', minHeight: '100vh' }}>
      {/* Mobile overlay */}
      {sidebarOpen && (
        <div
          onClick={() => setSidebarOpen(false)}
          style={{
            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)',
            zIndex: 998, display: 'none',
          }}
          className="sidebar-overlay"
        />
      )}

      {/* Sidebar */}
      <aside style={{
        width: 'var(--sidebar-width)',
        backgroundColor: 'var(--color-dark)',
        display: 'flex',
        flexDirection: 'column',
        position: 'fixed',
        top: 0,
        left: sidebarOpen ? 0 : undefined,
        bottom: 0,
        zIndex: 999,
        transition: 'transform 0.3s ease',
      }}>
        {/* Logo */}
        <div style={{
          padding: '24px 24px 20px',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
        }}>
          <div style={{
            fontSize: 24,
            fontWeight: 800,
            color: '#fff',
            letterSpacing: '-0.5px',
          }}>
            guardee
          </div>
          <button
            onClick={() => setSidebarOpen(false)}
            style={{
              background: 'none', border: 'none', color: '#fff',
              display: 'none', cursor: 'pointer',
            }}
            className="sidebar-close-btn"
          >
            <FiX size={20} />
          </button>
        </div>

        {/* Navigation */}
        <nav style={{ flex: 1, padding: '8px 12px', display: 'flex', flexDirection: 'column', gap: 4 }}>
          {allNavItems.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              onClick={() => setSidebarOpen(false)}
              style={({ isActive }) => linkStyle(isActive)}
            >
              <span style={{ fontSize: 18, display: 'flex' }}>{item.icon}</span>
              {item.label}
            </NavLink>
          ))}
        </nav>

        {/* User info */}
        <div style={{
          padding: '16px 20px',
          borderTop: '1px solid rgba(255,255,255,0.1)',
          display: 'flex',
          alignItems: 'center',
          gap: 12,
        }}>
          <div style={{
            width: 36,
            height: 36,
            borderRadius: '50%',
            background: 'var(--color-primary)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            color: '#fff',
            fontWeight: 600,
            fontSize: 14,
            flexShrink: 0,
          }}>
            {user?.first_name?.[0]?.toUpperCase() || user?.email?.[0]?.toUpperCase() || 'U'}
          </div>
          <div style={{ flex: 1, minWidth: 0 }}>
            <div style={{ color: '#fff', fontSize: 13, fontWeight: 600, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
              {user?.first_name ? `${user.first_name} ${user.last_name || ''}`.trim() : user?.email}
            </div>
            <div style={{ color: 'rgba(255,255,255,0.5)', fontSize: 11, textTransform: 'capitalize' }}>
              {user?.role}
            </div>
          </div>
          <button
            onClick={handleLogout}
            style={{
              background: 'none', border: 'none', color: 'rgba(255,255,255,0.5)',
              cursor: 'pointer', padding: 4, display: 'flex',
            }}
            title="Logout"
          >
            <FiLogOut size={16} />
          </button>
        </div>
      </aside>

      {/* Main content */}
      <div style={{ flex: 1, marginLeft: 'var(--sidebar-width)' }}>
        {/* Top bar */}
        <header style={{
          height: 64,
          background: 'var(--color-card-bg)',
          borderBottom: '1px solid var(--color-border)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          padding: '0 32px',
          position: 'sticky',
          top: 0,
          zIndex: 100,
        }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
            <button
              onClick={() => setSidebarOpen(true)}
              style={{
                background: 'none', border: 'none', cursor: 'pointer',
                display: 'none', padding: 4,
              }}
              className="mobile-menu-btn"
            >
              <FiMenu size={22} />
            </button>
            <div style={{
              display: 'flex', alignItems: 'center', gap: 8,
              background: '#f3f4f6', borderRadius: 8, padding: '8px 14px',
              minWidth: 280,
            }}>
              <FiSearch size={16} color="var(--color-text-muted)" />
              <input
                type="text"
                placeholder="Search campaigns, users..."
                style={{
                  border: 'none', background: 'none', outline: 'none',
                  fontSize: 14, color: 'var(--color-text)', width: '100%',
                }}
              />
            </div>
          </div>

          <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
            <NotificationBell />
            <Dropdown align="end">
              <Dropdown.Toggle
                variant="link"
                style={{
                  display: 'flex', alignItems: 'center', gap: 8,
                  textDecoration: 'none', color: 'var(--color-text)',
                  padding: '4px 8px', borderRadius: 8,
                }}
              >
                <div style={{
                  width: 32, height: 32, borderRadius: '50%',
                  background: 'var(--color-primary)', color: '#fff',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontWeight: 600, fontSize: 13,
                }}>
                  {user?.first_name?.[0]?.toUpperCase() || 'U'}
                </div>
                <span style={{ fontSize: 14, fontWeight: 500 }}>
                  {user?.first_name || 'User'}
                </span>
                <FiChevronDown size={14} />
              </Dropdown.Toggle>
              <Dropdown.Menu style={{ borderRadius: 'var(--radius-md)', border: '1px solid var(--color-border)', boxShadow: 'var(--shadow-lg)' }}>
                <Dropdown.Item onClick={handleLogout}>
                  <FiLogOut size={14} style={{ marginRight: 8 }} /> Logout
                </Dropdown.Item>
              </Dropdown.Menu>
            </Dropdown>
          </div>
        </header>

        {/* Content */}
        <main style={{ padding: 32 }}>
          <Outlet />
        </main>
      </div>

      <style>{`
        @media (max-width: 768px) {
          .sidebar-overlay { display: block !important; }
          .sidebar-close-btn { display: block !important; }
          .mobile-menu-btn { display: block !important; }
        }
      `}</style>
    </div>
  );
}
