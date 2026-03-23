import React from 'react';
import { Badge } from 'react-bootstrap';

const statusConfig = {
  draft: { bg: '#9ca3af', label: 'Draft' },
  pending_approval: { bg: '#f59e0b', label: 'Pending Approval' },
  approved: { bg: '#254ccb', label: 'Approved' },
  active: { bg: '#22c55e', label: 'Active' },
  completed: { bg: '#8b5cf6', label: 'Completed' },
  cancelled: { bg: '#ef4444', label: 'Cancelled' },
  invited: { bg: '#254ccb', label: 'Invited' },
  interested: { bg: '#f59e0b', label: 'Interested' },
  accepted: { bg: '#22c55e', label: 'Accepted' },
  declined: { bg: '#ef4444', label: 'Declined' },
  offer_sent: { bg: '#8b5cf6', label: 'Offer Sent' },
  offer_accepted: { bg: '#22c55e', label: 'Offer Accepted' },
  offer_declined: { bg: '#ef4444', label: 'Offer Declined' },
  content_submitted: { bg: '#3b82f6', label: 'Content Submitted' },
  content_approved: { bg: '#22c55e', label: 'Content Approved' },
  revision_requested: { bg: '#f59e0b', label: 'Revision Requested' },
  pending: { bg: '#f59e0b', label: 'Pending' },
};

export default function StatusBadge({ status }) {
  const config = statusConfig[status] || { bg: '#9ca3af', label: status };

  return (
    <Badge
      pill
      style={{
        backgroundColor: config.bg,
        color: '#fff',
        fontWeight: 500,
        fontSize: '12px',
        padding: '5px 12px',
        letterSpacing: '0.3px',
      }}
    >
      {config.label}
    </Badge>
  );
}
