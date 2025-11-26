document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.hhm-menu-toggle');
  const nav = document.querySelector('.hhm-nav');

  if (!toggle || !nav) return;

  toggle.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });
});

