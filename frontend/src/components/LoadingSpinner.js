import React from 'react';
import { Spinner } from 'react-bootstrap';

export default function LoadingSpinner() {
  return (
    <div style={{
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      minHeight: '200px',
      width: '100%',
    }}>
      <Spinner animation="border" style={{ color: 'var(--color-primary)' }} />
    </div>
  );
}
