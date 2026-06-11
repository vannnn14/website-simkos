<!-- Tailwind & Fonts -->
<link href="../public/tailwind.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
  (function() {
    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.classList.add('dark');
    }
  })();
  function toggleTheme() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
  }

  // ── Password visibility toggle ─────────────────────────────────────────────
  function togglePassword(id, btn) {
    var input = document.getElementById(id);
    if (input.type === 'password') {
      input.type = 'text';
      btn.querySelector('.eye-open').classList.add('hidden');
      btn.querySelector('.eye-closed').classList.remove('hidden');
    } else {
      input.type = 'password';
      btn.querySelector('.eye-open').classList.remove('hidden');
      btn.querySelector('.eye-closed').classList.add('hidden');
    }
  }

  // ── Toast Notification ──────────────────────────────────────────────────────
  function showToast(message, type) {
    type = type || 'success';
    var existing = document.getElementById('globalToast');
    if (existing) existing.remove();

    var colors = {
      success: 'bg-green-100 text-green-700 border-green-200',
      error: 'bg-red-100 text-red-700 border-red-200',
      warning: 'bg-yellow-100 text-yellow-700 border-yellow-200',
      info: 'bg-blue-100 text-blue-700 border-blue-200'
    };
    var icons = {
      success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
      error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
      warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>',
      info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };

    var toast = document.createElement('div');
    toast.id = 'globalToast';
    toast.className = 'fixed bottom-6 right-6 z-[999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl border text-sm font-medium transition-all duration-500 translate-y-4 opacity-0 ' + (colors[type] || colors.success);
    toast.innerHTML = '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' + (icons[type] || icons.success) + '</svg><span>' + message + '</span>';
    document.body.appendChild(toast);

    requestAnimationFrame(function() {
      toast.classList.remove('translate-y-4', 'opacity-0');
    });

    setTimeout(function() {
      toast.classList.add('translate-y-4', 'opacity-0');
      setTimeout(function() { toast.remove(); }, 500);
    }, 3500);
  }
</script>

<style>body{font-family:'Inter',sans-serif}</style>
