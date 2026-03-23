import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import AppLayout from './layouts/AppLayout';
import AuthLayout from './layouts/AuthLayout';
import ProtectedRoute from './components/ProtectedRoute';
import LoginPage from './pages/auth/LoginPage';
import RegisterPage from './pages/auth/RegisterPage';
import DashboardPage from './pages/dashboard/DashboardPage';
import CampaignListPage from './pages/campaigns/CampaignListPage';
import CampaignCreatePage from './pages/campaigns/CampaignCreatePage';
import CampaignDetailPage from './pages/campaigns/CampaignDetailPage';
import PendingApprovalsPage from './pages/campaigns/PendingApprovalsPage';
import MyInvitationsPage from './pages/invitations/MyInvitationsPage';
import OfferViewPage from './pages/offers/OfferViewPage';
import UploadPage from './pages/assets/UploadPage';
import UserListPage from './pages/admin/UserListPage';

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Navigate to="/dashboard" replace />} />

      <Route element={<AuthLayout />}>
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />
      </Route>

      <Route element={<ProtectedRoute />}>
        <Route element={<AppLayout />}>
          <Route path="/dashboard" element={<DashboardPage />} />
          <Route path="/campaigns" element={<CampaignListPage />} />
          <Route path="/campaigns/pending" element={<ProtectedRoute roles={['admin']}><PendingApprovalsPage /></ProtectedRoute>} />
          <Route path="/campaigns/create" element={<ProtectedRoute roles={['brand', 'admin']}><CampaignCreatePage /></ProtectedRoute>} />
          <Route path="/campaigns/:id" element={<CampaignDetailPage />} />
          <Route path="/invitations" element={<ProtectedRoute roles={['influencer']}><MyInvitationsPage /></ProtectedRoute>} />
          <Route path="/offers/:id" element={<OfferViewPage />} />
          <Route path="/upload" element={<ProtectedRoute roles={['influencer']}><UploadPage /></ProtectedRoute>} />
          <Route path="/admin/users" element={<ProtectedRoute roles={['admin']}><UserListPage /></ProtectedRoute>} />
        </Route>
      </Route>
    </Routes>
  );
}
