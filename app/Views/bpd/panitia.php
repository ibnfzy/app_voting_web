<?= $this->extend('bpd/base'); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">Tambah
          Data</button>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered datattables">
          <thead>
            <th>~</th>
            <th>Username</th>
            <th>Panitia/BPD</th>
            <th>Aksi</th>
          </thead>
          <tbody>
            <?php foreach ($data as $key => $item) : ?>
              <tr>
                <td><?= $key + 1; ?></td>
                <td><?= $item['username']; ?></td>
                <td><?= strtoupper($item['role']); ?></td>
                <td>
                  <button class="btn btn-primary"
                    onclick="edit('<?= $item['id_user'] ?>', '<?= $item['username'] ?>', '<?= $item['role'] ?>')">edit</button>
                  <a href="/BPDPanel/Panitia/<?= $item['id_user'] ?>" class="btn btn-danger">Hapus</a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="tambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahLabel">Tambah Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/BPDPanel/Panitia" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
              <option value="panitia">Panitia</option>
              <option value="bpd">BPD</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="edit" tabindex="-1" aria-labelledby="editLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editLabel">Edit Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/BPDPanel/Panitia/Update" method="post">
        <input type="hidden" name="id_user" id="id_user">
        <div class="modal-body">
          <div class="mb-3">
            <label for="username-edit">Username</label>
            <input type="text" name="username" id="username-edit" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="password-id">Password baru</label>
            <input type="password" name="password" class="form-control">
            <small class="text-sm text-danger font-weight-bold">Kosongkan jika tidak ingin mengganti password</small>
          </div>

          <div class="mb-3">
            <label for="role-edit">Role</label>
            <select name="role" id="role-edit" class="form-control" required>
              <option value="panitia">Panitia</option>
              <option value="bpd">BPD</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>

<script>
  const edit = (id, name, role) => {
    $('#id_user').val(id)
    $('#username-edit').val(name)
    $(`#role-edit option[value=${role}]`).attr('selected')
    const myModal = new bootstrap.Modal(document.getElementById('edit'))
    myModal.show()
  };
</script>

<?= $this->endSection(); ?>