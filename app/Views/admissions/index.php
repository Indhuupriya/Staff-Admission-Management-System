<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Admissions</h4>
  <button class="btn btn-success" id="openAddAdmission">New Admission</button>
</div>

<table id="admissions_table" class="display" style="width:100%">
  <thead>
    <tr>
      <th>ID</th><th>Patient</th><th>Phone</th><th>Date</th><th>Type</th><th>Created By</th><th>Status</th><th>Action</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Admission Modal -->
<div class="modal fade" id="admissionModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="admissionForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Admission</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?= csrf_field() ?>
        <input type="hidden" id="admission_id">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Patient Name</label>
            <input id="patient_name" name="patient_name" class="form-control" required>
          </div>
          <div class="form-group col-md-6">
            <label>Patient Phone</label>
            <input id="patient_phone" name="patient_phone" class="form-control">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Admission Date</label>
            <input id="admission_date" name="admission_date" class="form-control" type="datetime-local">
          </div>
          <div class="form-group col-md-6">
            <label>Admission Type</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="admission_type" value="OP" checked> OP
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="admission_type" value="IP"> IP
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Patient Signature</label>
          <div>
            <canvas id="sigCanvas" width="600" height="200"></canvas>
          </div>
          <div class="mt-2">
            <button type="button" id="clearSig" class="btn btn-sm btn-secondary">Clear</button>
            <button type="button" id="saveSig" class="btn btn-sm btn-primary">Capture</button>
            <small class="text-muted ml-2">Captured signature will be saved as image data.</small>
          </div>
          <input type="hidden" id="patient_signature" name="patient_signature">
        </div>

        <div class="form-group">
          <label>Branch (Created Branch)</label>
          <input id="created_branch" name="created_branch" class="form-control" value="<?= esc(session()->get('staff_name') ? session()->get('staff_name') : '') ?>">
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveAdmission">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
$(function(){
  var canvas = document.getElementById('sigCanvas');
  var signaturePad = new SignaturePad(canvas);

  $('#clearSig').click(function(){ signaturePad.clear(); $('#patient_signature').val(''); });
  $('#saveSig').click(function(){
    if(signaturePad.isEmpty()){ alert('Please sign first'); return; }
    const dataUrl = signaturePad.toDataURL(); // PNG base64
    $('#patient_signature').val(dataUrl);
    alert('Signature captured');
  });
  const table = $('#admissions_table').DataTable({
    ajax: {
      url: '<?= base_url("api/admissions") ?>',
      dataSrc: 'data',
      beforeSend: function(xhr){
            xhr.setRequestHeader('Authorization', 'Bearer <?= session()->get('jwt_token') ?>');
        }
    },
    columns: [
      { data: 'id' },
      { data: 'patient_name' },
      { data: 'patient_phone' },
      { data: 'admission_date' },
      { data: 'admission_type' },
      { data: 'staff_name' },
      { data: 'status', render: function(data,type,row){
          return '<select class="form-control form-control-sm admission-status" data-id="'+row.id+'">' +
            '<option '+(data=='Admitted'?'selected':'')+'>Admitted</option>' +
            '<option '+(data=='Treatment In Progress'?'selected':'')+'>Treatment In Progress</option>' +
            '<option '+(data=='Discharged'?'selected':'')+'>Discharged</option>' +
          '</select>';
        }},
      { data: null, render: function(row){
          return '<button class="btn btn-sm btn-info viewAdmission" data-id="'+row.id+'">View</button>';
        }}
    ]
  });
  $('#openAddAdmission').click(function(){
    $('#admissionForm')[0].reset();
    $('#admission_id').val('');
    signaturePad.clear();
    $('#patient_signature').val('');
    $('#admissionModal').modal('show');
  });
  $('#admissionForm').submit(function(e){
    e.preventDefault();
    const payload = {
      patient_name: $('#patient_name').val(),
      patient_phone: $('#patient_phone').val(),
      admission_date: $('#admission_date').val(),
      admission_type: $('input[name=admission_type]:checked').val(),
      patient_signature: $('#patient_signature').val(),
      status: 'Admitted',
      created_by: <?= session()->get('staff_id') ?? 0 ?>,
      created_branch: $('#created_branch').val()
    };
    const id = $('#admission_id').val();
    const url = id ? '<?= base_url("api/admissions") ?>/' + id : '<?= base_url("api/admissions") ?>';
    const method = id ? 'PUT' : 'POST';
    $.ajax({
      url: url,
      method: method,
      data: JSON.stringify(payload),
      contentType: 'application/json',
      success: function(){ $('#admissionModal').modal('hide'); table.ajax.reload(null,false); alert('Saved'); },
      error: function(xhr){ alert('Error: ' + xhr.responseText); }
    });
  });
  $('#admissions_table').on('change', '.admission-status', function(){
    const id = $(this).data('id'), status = $(this).val();
    $.ajax({
      url: '<?= base_url("api/admissions") ?>/' + id + '/status',
      method: 'PATCH',
      data: JSON.stringify({ status: status }),
      contentType: 'application/json',
      success: function(){ table.ajax.reload(null,false); }
    });
  });
  $('#admissions_table').on('click', '.viewAdmission', function(){
    const id = $(this).data('id');
    // fetch the row data from table cache
    const row = table.rows().data().toArray().find(r => r.id == id);
    if(!row) return alert('Not found');
    $('#admission_id').val(row.id);
    $('#patient_name').val(row.patient_name);
    $('#patient_phone').val(row.patient_phone);
    if(row.admission_date) $('#admission_date').val(row.admission_date.replace(' ', 'T'));
    $('input[name=admission_type][value="'+row.admission_type+'"]').prop('checked', true);
    $('#created_branch').val(row.created_branch || '');
    if(row.patient_signature){
      const win = window.open("");
      win.document.write('<img src="'+row.patient_signature+'" alt="signature">');
    } else {
      alert('No signature available');
    }
    $('#admissionModal').modal('show');
  });

});
</script>

<?= $this->endSection() ?>
