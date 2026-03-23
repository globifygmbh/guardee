import React from 'react';

export default function KPICard({ icon, label, value, color = 'var(--color-primary)' }) {
  return (
    <div style={{
      background: 'var(--color-card-bg)',
      borderRadius: 'var(--radius-lg)',
      boxShadow: 'var(--shadow-md)',
      padding: '24px',
      display: 'flex',
      alignItems: 'center',
      gap: '16px',
    }}>
      <div style={{
        width: 48,
        height: 48,
        borderRadius: 'var(--radius-md)',
        background: `${color}15`,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: '22px',
        color: color,
        flexShrink: 0,
      }}>
        {icon}
      </div>
      <div>
        <div style={{
          fontSize: '28px',
          fontWeight: 700,
          color: 'var(--color-text)',
          lineHeight: 1.1,
        }}>
          {value}
        </div>
        <div style={{
          fontSize: '13px',
          color: 'var(--color-text-muted)',
          fontWeight: 500,
          marginTop: 2,
        }}>
          {label}
        </div>
      </div>
    </div>
  );
}
