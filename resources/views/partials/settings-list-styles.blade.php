<style>
  .settings-page-wrap {
    display: flex;
    gap: 24px;
    max-width: 1140px;
    align-items: flex-start;
  }
  .settings-main { flex: 1; min-width: 0; }

  /* Page section header */
  .settings-section-header {
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 24px;
  }
  .settings-section-icon {
    width: 48px; height: 48px; border-radius: 14px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
    box-shadow: 0 4px 14px color-mix(in srgb, var(--color-primary) 35%, transparent);
  }
  .settings-section-title {
    font-size: 1.4rem; font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--color-primary-text-dark);
    margin: 0;
  }
  .settings-section-sub {
    font-size: 0.82rem; color: #9ca3af; margin: 4px 0 0;
  }

  /* Add form panel */
  .lookup-add-header {
    padding: 14px 20px 0;
    font-size: 0.72rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--color-primary);
  }
  .lookup-add-body { padding: 14px 20px 18px; }
  .lookup-add-form {
    display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;
  }
  .lookup-add-field { flex: 1; min-width: 200px; }
  .lookup-add-color { min-width: 200px; }
  .lookup-add-action { padding-bottom: 0; }

  /* Color helpers */
  .color-swatch-sm {
    width: 38px; height: 38px; border-radius: 10px;
    border: 1.5px solid #e5e7eb; overflow: hidden; flex-shrink: 0; cursor: pointer;
  }
  .color-swatch-sm input[type=color] {
    width: 100%; height: 100%; border: none; padding: 0; cursor: pointer; background: none;
  }
  .color-hex-sm { flex: 1; font-family: 'Courier New', monospace; font-size: 0.83rem; }
  .color-dot {
    width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
    box-shadow: 0 0 0 2px #fff, 0 0 0 3px #e5e7eb;
  }

  /* List items */
  .lookup-item {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.12s;
  }
  .lookup-item:last-child { border-bottom: none; }
  .lookup-item:hover { background: color-mix(in srgb, var(--color-primary) 2%, #fff); }
  .lookup-item-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: var(--color-primary-subtle);
    display: flex; align-items: center; justify-content: center;
    color: var(--color-primary); flex-shrink: 0;
  }
  .lookup-item-name { font-size: 0.9rem; font-weight: 600; color: #111827; }
  .type-color-swatch {
    width: 30px; height: 30px; border-radius: 8px;
    display: inline-block; flex-shrink: 0;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);
  }

  /* Status pills */
  .lookup-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.72rem; font-weight: 700;
    padding: 3px 10px; border-radius: 20px; flex-shrink: 0;
  }
  .lookup-status-pill .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
  .lookup-status-pill.active   { background: #d1fae5; color: #065f46; }
  .lookup-status-pill.active .dot   { background: #10b981; }
  .lookup-status-pill.inactive { background: #f3f4f6; color: #6b7280; }
  .lookup-status-pill.inactive .dot { background: #d1d5db; }

  /* Direction pills */
  .direction-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.72rem; font-weight: 700;
    padding: 3px 10px; border-radius: 20px; flex-shrink: 0;
  }
  .direction-pill.credit { background: #d1fae5; color: #065f46; }
  .direction-pill.debit  { background: #fee2e2; color: #991b1b; }

  /* Action buttons */
  .lookup-actions { display: flex; gap: 6px; flex-shrink: 0; }
  .btn-icon-sm {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 8px;
    border: 1.5px solid #e5e7eb; background: #fff;
    color: #6b7280; cursor: pointer; transition: all 0.15s;
  }
  .btn-icon-sm.danger:hover { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }
  .btn-icon-sm.warn:hover   { border-color: #fde68a; background: #fffbeb; color: #d97706; }
  .btn-icon-sm.success:hover{ border-color: #a7f3d0; background: #f0fdf4; color: #059669; }

  /* Empty state */
  .lookup-empty {
    padding: 48px 24px; text-align: center; color: #9ca3af;
  }
  .lookup-empty svg { opacity: 0.3; margin-bottom: 12px; }
  .lookup-empty p { font-size: 0.85rem; margin: 0; }
</style>
