<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Staff Management</h4>
  <button class="btn btn-success" id="openAddStaff">Add Staff</button>
</div>

<table id="staffs_table" class="display" style="width:100%">
  <thead>
    <tr>
      <th>ID</th><th>Name</th><th>Username</th><th>Branch</th><th>Mobile</th><th>Gender</th><th>Locations</th><th>Actions</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="staffModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="staffForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add/Edit Staff</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="staff_id">

        <div class="form-group">
          <label>Name</label>
          <input name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
          <label>Code</label>
          <input name="code" id="code" class="form-control">
        </div>

        <div class="form-group">
          <label>Branch</label>
          <input name="branch" id="branch" class="form-control">
        </div>

        <div class="form-group">
          <label>Username</label>
          <input name="username" id="username" class="form-control" required>
        </div>

        <div class="form-group">
          <label>Password <small>(leave blank to keep unchanged on edit)</small></label>
          <input name="password" id="password" type="password" class="form-control">
        </div>

        <div class="form-group">
          <label>Mobile</label>
          <input name="mobile" id="mobile" class="form-control">
        </div>

        <div class="form-group">
          <label>Gender</label>
          <select name="gender" id="gender" class="form-control">
            <option>Male</option><option>Female</option><option>Other</option>
          </select>
        </div>

        <div class="form-group">
          <label>Locations</label>
          <div>
            <?php foreach($locations as $loc): ?>
              <div class="form-check form-check-inline">
                <input class="form-check-input loc-checkbox" type="checkbox" id="loc_<?= $loc['id'] ?>" value="<?= $loc['id'] ?>">
                <label class="form-check-label" for="loc_<?= $loc['id'] ?>"><?= esc($loc['name']) ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveStaff">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
$(function(){
  // DataTable
  const table = $('#staffs_table').DataTable({
    ajax: {
      url: '<?= base_url("api/staffs") ?>',
      dataSrc: 'data',
      beforeSend: function(xhr){
            xhr.setRequestHeader('Authorization', 'Bearer <?= session()->get('jwt_token') ?>');
        }
    },
    columns: [
      { data: 'id' },
      { data: 'name' },
      { data: 'username' },
      { data: 'branch' },
      { data: 'mobile' },
      { data: 'gender' },
      { data: 'locations', render: function(d){
          if(!d || d.length === 0) return '<small class="text-muted">—</small>';
          return d.join(', '); // join array of location names
      }},
      { data: null, render: function(row){
          return '<button class="btn btn-sm btn-primary editStaff" data-id="'+row.id+'">Edit</button> ' +
                 '<button class="btn btn-sm btn-danger deleteStaff" data-id="'+row.id+'">Delete</button>';
      }}
    ]
  });

  // open add modal
  $('#openAddStaff').click(function(){
    $('#staffForm')[0].reset();
    $('#staff_id').val('');
    $('.loc-checkbox').prop('checked', false);
    $('#staffModal').modal('show');
  });

  // edit
  $('#staffs_table').on('click', '.editStaff', function(){
    const id = $(this).data('id');
    $.getJSON('<?= base_url("api/staffs") ?>', function(res){
      // find the staff object
      const staff = res.data.find(s => s.id == id);
      if(!staff) return alert('Not found');
      $('#staff_id').val(staff.id);
      $('#name').val(staff.name);
      $('#code').val(staff.code);
      $('#branch').val(staff.branch);
      $('#username').val(staff.username);
      $('#mobile').val(staff.mobile);
      $('#gender').val(staff.gender);
      if(staff.location_ids && Array.isArray(staff.location_ids)){
        const locIds = staff.location_ids.map(Number); // ensure all are numbers
          $('.loc-checkbox').each(function(){ 
            const val = Number($(this).val()); // current checkbox value
            $(this).prop('checked', locIds.includes(val));
          });
      } else {
          $('.loc-checkbox').prop('checked', false);
      }

      $('#staffModal').modal('show');
    });
  });

  // save 
  $('#staffForm').submit(function(e){
    e.preventDefault();
    const id = $('#staff_id').val();
    const payload = {
      name: $('#name').val(),
      code: $('#code').val(),
      branch: $('#branch').val(),
      username: $('#username').val(),
      password: $('#password').val(),
      mobile: $('#mobile').val(),
      gender: $('#gender').val(),
      locations: $('.loc-checkbox:checked').map(function(){ return parseInt(this.value) }).get()
    };
    const url = id ? '<?= base_url("api/staffs") ?>/'+id : '<?= base_url("api/staffs") ?>';
    const method = id ? 'PUT' : 'POST';
    $.ajax({
      url: url,
      method: method,
      data: JSON.stringify(payload),
      contentType: 'application/json',
      success: function(res){
        $('#staffModal').modal('hide');
        $('#staffForm')[0].reset();
        table.ajax.reload(null,false);
        alert('Saved');
      },
      error: function(xhr){
        alert('Error: ' + xhr.responseText);
      }
    });
  });

  // delete
  $('#staffs_table').on('click', '.deleteStaff', function(){
    if(!confirm('Delete this staff?')) return;
    const id = $(this).data('id');
    $.ajax({
      url: '<?= base_url("api/staffs") ?>/'+id,
      method: 'DELETE',
      success: function(){ table.ajax.reload(null,false); alert('Deleted') }
    });
  });

});
</script>

<?= $this->endSection() ?>
