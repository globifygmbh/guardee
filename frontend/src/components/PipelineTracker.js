import React from 'react';
import { FiFileText, FiCheckCircle, FiSend, FiThumbsUp, FiDollarSign, FiCamera, FiFlag } from 'react-icons/fi';

const steps = [
  { key: 'created', label: 'Created', icon: <FiFileText /> },
  { key: 'approved', label: 'Approved', icon: <FiCheckCircle /> },
  { key: 'invited', label: 'Invited', icon: <FiSend /> },
  { key: 'accepted', label: 'Accepted', icon: <FiThumbsUp /> },
  { key: 'offer_sent', label: 'Offer Sent', icon: <FiDollarSign /> },
  { key: 'content', label: 'Content', icon: <FiCamera /> },
  { key: 'done', label: 'Done', icon: <FiFlag /> },
];

export default function PipelineTracker({ counts = {}, activeStep }) {
  return (
    <div style={{
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between',
      padding: '24px 0',
      overflowX: 'auto',
    }}>
      {steps.map((step, index) => {
        const isActive = step.key === activeStep;
        const count = counts[step.key] || 0;

        return (
          <React.Fragment key={step.key}>
            {index > 0 && (
              <div style={{
                flex: 1,
                height: 2,
                backgroundColor: '#e5e5e5',
                margin: '0 4px',
                minWidth: 20,
              }} />
            )}
            <div style={{
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
              gap: 8,
              flexShrink: 0,
            }}>
              <div style={{
                width: 44,
                height: 44,
                borderRadius: '50%',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                fontSize: 18,
                backgroundColor: isActive ? 'var(--color-primary)' : '#f3f4f6',
                color: isActive ? '#fff' : 'var(--color-text-muted)',
                fontWeight: 600,
                transition: 'all 0.2s',
              }}>
                {step.icon}
              </div>
              <span style={{
                fontSize: 11,
                fontWeight: isActive ? 600 : 500,
                color: isActive ? 'var(--color-primary)' : 'var(--color-text-muted)',
                textAlign: 'center',
                whiteSpace: 'nowrap',
              }}>
                {step.label}
              </span>
              <span style={{
                fontSize: 18,
                fontWeight: 700,
                color: isActive ? 'var(--color-primary)' : 'var(--color-text)',
              }}>
                {count}
              </span>
            </div>
          </React.Fragment>
        );
      })}
    </div>
  );
}
