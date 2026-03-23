import React, { useState, useEffect, useRef } from 'react';
import { FiBell } from 'react-icons/fi';
import api from '../services/api';

export default function NotificationBell() {
  const [notifications, setNotifications] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [open, setOpen] = useState(false);
  const ref = useRef(null);

  useEffect(() => {
    fetchNotifications();
  }, []);

  useEffect(() => {
    function handleClickOutside(event) {
      if (ref.current && !ref.current.contains(event.target)) {
        setOpen(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const fetchNotifications = async () => {
    try {
      const res = await api.get('/notifications');
      setNotifications(res.data || []);
      setUnreadCount((res.data || []).filter((n) => !n.read).length);
    } catch {
      setNotifications([]);
      setUnreadCount(0);
    }
  };

  const markAsRead = async (id) => {
    try {
      await api.put(`/notifications/${id}/read`);
      setNotifications((prev) =>
        prev.map((n) => (n.id === id ? { ...n, read: true } : n))
      );
      setUnreadCount((prev) => Math.max(0, prev - 1));
    } catch {
      // silently fail
    }
  };

  return (
    <div ref={ref} style={{ position: 'relative' }}>
      <button
        onClick={() => setOpen(!open)}
        style={{
          background: 'none',
          border: 'none',
          cursor: 'pointer',
          position: 'relative',
          padding: 8,
          borderRadius: 8,
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <FiBell size={20} color="var(--color-text)" />
        {unreadCount > 0 && (
          <span style={{
            position: 'absolute',
            top: 2,
            right: 2,
            width: 18,
            height: 18,
            borderRadius: '50%',
            backgroundColor: 'var(--color-danger)',
            color: '#fff',
            fontSize: 10,
            fontWeight: 700,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          }}>
            {unreadCount > 9 ? '9+' : unreadCount}
          </span>
        )}
      </button>

      {open && (
        <div style={{
          position: 'absolute',
          top: '100%',
          right: 0,
          width: 340,
          maxHeight: 400,
          overflowY: 'auto',
          background: 'var(--color-card-bg)',
          borderRadius: 'var(--radius-md)',
          boxShadow: 'var(--shadow-lg)',
          zIndex: 1000,
          border: '1px solid var(--color-border)',
          marginTop: 8,
        }}>
          <div style={{ padding: '16px 20px', borderBottom: '1px solid var(--color-border)', fontWeight: 600 }}>
            Notifications
          </div>
          {notifications.length === 0 ? (
            <div style={{ padding: '32px 20px', textAlign: 'center', color: 'var(--color-text-muted)', fontSize: 14 }}>
              No notifications yet
            </div>
          ) : (
            notifications.slice(0, 10).map((n) => (
              <div
                key={n.id}
                onClick={() => markAsRead(n.id)}
                style={{
                  padding: '12px 20px',
                  borderBottom: '1px solid var(--color-border)',
                  cursor: 'pointer',
                  backgroundColor: n.read ? 'transparent' : 'rgba(37, 76, 203, 0.04)',
                  transition: 'background-color 0.2s',
                }}
              >
                <div style={{ fontSize: 13, fontWeight: n.read ? 400 : 500 }}>
                  {n.message}
                </div>
                <div style={{ fontSize: 11, color: 'var(--color-text-muted)', marginTop: 4 }}>
                  {n.created_at ? new Date(n.created_at).toLocaleDateString() : ''}
                </div>
              </div>
            ))
          )}
        </div>
      )}
    </div>
  );
}
