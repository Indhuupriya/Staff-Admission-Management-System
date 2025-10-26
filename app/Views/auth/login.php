<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <div class="card">
      <div class="card-header">Staff Login</div>
      <div class="card-body">
        <form method="post" action="<?= base_url('/login') ?>">
          <?= csrf_field() ?>
          <div class="form-group">
            <label>Username</label>
            <input name="username" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input name="password" type="password" class="form-control" required>
          </div>
          <button class="btn btn-primary">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
