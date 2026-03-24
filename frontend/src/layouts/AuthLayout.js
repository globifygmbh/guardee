import React from 'react';
import { Outlet } from 'react-router-dom';

export default function AuthLayout() {
  return (
    <div style={{
      minHeight: '100vh',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      backgroundColor: 'var(--color-bg)',
      padding: 24,
    }}>
      <div style={{
        width: '100%',
        maxWidth: 440,
      }}>
        <div style={{ textAlign: 'center', marginBottom: 32 }}>
          <h1 style={{
            fontSize: 32,
            fontWeight: 800,
            color: 'var(--color-text)',
            letterSpacing: '-0.5px',
          }}>
            guardee
          </h1>
          <p style={{ color: 'var(--color-text-muted)', fontSize: 14, marginTop: 4 }}>
            Influencer Marketplace Platform
          </p>
        </div>
        <div style={{
          background: 'var(--color-card-bg)',
          borderRadius: 'var(--radius-lg)',
          boxShadow: 'var(--shadow-md)',
          padding: '36px 32px',
        }}>
          <Outlet />
        </div>
      </div>
    </div>
  );
}
