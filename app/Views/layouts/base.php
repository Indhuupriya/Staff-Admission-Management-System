<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= esc($title ?? 'Staff & Admissions') ?></title>

  <!-- CSRF meta -->
  <meta name="csrf-name" content="<?= csrf_token() ?>">
  <meta name="csrf-hash" content="<?= csrf_hash() ?>">

  <!-- Assets (CDN) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

  <style>
    /* small helpers */
    .mt-20{ margin-top:20px; }
    canvas{ background:#fff; border:1px solid #ddd; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand bg-light">
    <a class="navbar-brand" href="<?= base_url('/') ?>">StaffAdmission</a>
    <div class="ml-auto">
      <?php if(session()->get('staff_name')): ?>
        <span class="mr-3">Hi, <?= esc(session()->get('staff_name')) ?></span>
        <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="container mt-4">
    <?= $this->renderSection('content') ?>
  </div>

  <script>
  // Global JS: provide csrf data and JWT header for AJAX requests
  const CSRF_NAME = $('meta[name="csrf-name"]').attr('content');
  let CSRF_HASH = $('meta[name="csrf-hash"]').attr('content');
  const JWT_TOKEN = "<?= session()->get('jwt_token') ?? '' ?>";

  // Attach CSRF and Authorization for jQuery AJAX automatically
  $.ajaxSetup({
    beforeSend: function(xhr, settings){
      // Add CSRF as header or a field based on server config
      if(CSRF_NAME && CSRF_HASH){
        xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_HASH);
      }
      if(JWT_TOKEN){
        xhr.setRequestHeader('Authorization', 'Bearer ' + JWT_TOKEN);
      }
      xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
    }
  });

  // update CSRF token when server returns a new one (optional)
  $(document).ajaxComplete(function(event, xhr, settings){
    const tokenName = CSRF_NAME;
    const newHash = xhr.getResponseHeader('X-CSRF-HASH');
    if(newHash){
      CSRF_HASH = newHash;
      $('meta[name="csrf-hash"]').attr('content', newHash);
    }
  });
  </script>
</body>
</html>
