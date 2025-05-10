import './bootstrap';
import '../css/app.css';
import axios from 'axios';
// resources/js/app.js

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('darkToggle');
  const html = document.documentElement;

  toggleBtn?.addEventListener('click', () => {
    html.classList.toggle('dark');
    localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
  });

  // Load saved theme on page load
  if (localStorage.getItem('theme') === 'dark') {
    html.classList.add('dark');
  }
});
