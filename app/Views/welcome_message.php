<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<header style="background-color:#dd4814; color:#fff; padding:2rem 1rem; text-align:center;">
    <h1>Task: Staff & Admission Management System</h1>
</header>

<section style="margin:2rem auto; max-width:800px; padding:1rem; background-color:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,.1); text-align:center;">
    <p style="font-size:1.2rem; margin-bottom:2rem;">
        This system allows you to manage staff members and admissions efficiently. Click the buttons below to view and manage Admissions or Staffs.
    </p>

    <div style="display:flex; justify-content:center; gap:1.5rem;">
        <a href="<?= base_url('admissions') ?>" style="padding:1rem 2rem; background-color:#dd4814; color:#fff; text-decoration:none; border-radius:5px; font-weight:bold; transition:background-color 0.3s;">
            Admissions
        </a>
        <a href="<?= base_url('staffs') ?>" style="padding:1rem 2rem; background-color:#dd4814; color:#fff; text-decoration:none; border-radius:5px; font-weight:bold; transition:background-color 0.3s;">
            Staffs
        </a>
    </div>
</section>

<?= $this->endSection() ?>
