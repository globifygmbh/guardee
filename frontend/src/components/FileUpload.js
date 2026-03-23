import React, { useCallback, useState } from 'react';
import { useDropzone } from 'react-dropzone';
import { ProgressBar } from 'react-bootstrap';
import { FiUploadCloud, FiFile, FiImage, FiVideo, FiX } from 'react-icons/fi';

export default function FileUpload({ onFilesSelected, accept, maxFiles = 10 }) {
  const [files, setFiles] = useState([]);

  const onDrop = useCallback((acceptedFiles) => {
    const newFiles = acceptedFiles.map((file) => ({
      file,
      id: Math.random().toString(36).substr(2, 9),
      preview: file.type.startsWith('image/') ? URL.createObjectURL(file) : null,
      progress: 0,
    }));
    setFiles((prev) => {
      const updated = [...prev, ...newFiles];
      if (onFilesSelected) onFilesSelected(updated.map((f) => f.file));
      return updated;
    });
  }, [onFilesSelected]);

  const removeFile = (id) => {
    setFiles((prev) => {
      const updated = prev.filter((f) => f.id !== id);
      if (onFilesSelected) onFilesSelected(updated.map((f) => f.file));
      return updated;
    });
  };

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    onDrop,
    accept: accept || {
      'image/*': ['.png', '.jpg', '.jpeg', '.gif', '.webp'],
      'video/*': ['.mp4', '.mov', '.avi'],
      'application/pdf': ['.pdf'],
    },
    maxFiles,
  });

  const getFileIcon = (type) => {
    if (type.startsWith('image/')) return <FiImage size={20} />;
    if (type.startsWith('video/')) return <FiVideo size={20} />;
    return <FiFile size={20} />;
  };

  return (
    <div>
      <div
        {...getRootProps()}
        style={{
          border: `2px dashed ${isDragActive ? 'var(--color-primary)' : '#d1d5db'}`,
          borderRadius: 'var(--radius-lg)',
          padding: '48px 24px',
          textAlign: 'center',
          cursor: 'pointer',
          transition: 'all 0.2s',
          backgroundColor: isDragActive ? 'rgba(37, 76, 203, 0.04)' : 'transparent',
        }}
      >
        <input {...getInputProps()} />
        <FiUploadCloud size={40} color={isDragActive ? 'var(--color-primary)' : '#9ca3af'} />
        <p style={{ marginTop: 12, marginBottom: 4, fontWeight: 500, color: 'var(--color-text)' }}>
          {isDragActive ? 'Drop files here' : 'Drag & drop files here, or click to browse'}
        </p>
        <p style={{ fontSize: 13, color: 'var(--color-text-muted)', margin: 0 }}>
          Images, videos, or PDFs
        </p>
      </div>

      {files.length > 0 && (
        <div style={{ marginTop: 16, display: 'flex', flexDirection: 'column', gap: 8 }}>
          {files.map((f) => (
            <div
              key={f.id}
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: 12,
                padding: '12px 16px',
                borderRadius: 'var(--radius-sm)',
                border: '1px solid var(--color-border)',
                background: '#fafafa',
              }}
            >
              {f.preview ? (
                <img
                  src={f.preview}
                  alt=""
                  style={{ width: 40, height: 40, borderRadius: 8, objectFit: 'cover' }}
                />
              ) : (
                <div style={{
                  width: 40,
                  height: 40,
                  borderRadius: 8,
                  background: '#f3f4f6',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: 'var(--color-text-muted)',
                }}>
                  {getFileIcon(f.file.type)}
                </div>
              )}
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontSize: 13, fontWeight: 500, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                  {f.file.name}
                </div>
                <div style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>
                  {(f.file.size / 1024 / 1024).toFixed(2)} MB
                </div>
                {f.progress > 0 && f.progress < 100 && (
                  <ProgressBar now={f.progress} style={{ height: 4, marginTop: 4 }} />
                )}
              </div>
              <button
                onClick={(e) => { e.stopPropagation(); removeFile(f.id); }}
                style={{
                  border: 'none',
                  background: 'none',
                  cursor: 'pointer',
                  color: 'var(--color-text-muted)',
                  padding: 4,
                }}
              >
                <FiX size={16} />
              </button>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
