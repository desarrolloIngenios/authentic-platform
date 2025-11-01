document.addEventListener('click', function (e) {
  const openBtn = e.target.closest('[data-modal-target]');
  if (openBtn) {
    e.preventDefault();
    const modalId = openBtn.getAttribute('data-modal-target');
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('show');
    }
  }

  if (e.target.matches('.custom-modal-close, .btn-close-custom')) {
    const modal = e.target.closest('.custom-modal-overlay');
    if (modal) {
      modal.classList.remove('show');
    }
  }

  const overlay = e.target.classList.contains('custom-modal-overlay') ? e.target : null;
  if (overlay) {
    overlay.classList.remove('show');
  }
});

document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.custom-modal-overlay.show').forEach(modal => {
      modal.classList.remove('show');
    });
  }
});
