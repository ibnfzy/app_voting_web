<?= $this->extend('panitia/base'); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="card">
    <div class="card-header">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">Tambah Data</button>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered datatables">
        <thead>
          <th>~</th>
          <th>Nama</th>
          <th>Foto</th>
          <th>Visi</th>
          <th>Misi</th>
          <th>Aksi</th>
        </thead>
        <tbody>
          <?php foreach ($data as $key => $item) : ?>
            <tr>
              <td><?= $key + 1; ?></td>
              <td><?= $item['name']; ?></td>
              <td><img src="/uploads/<?= $item['photo'] ?>" alt="" width="200" class="img-fluid"></td>
              <td>
                <?= $item['visi']; ?>
              </td>
              <td>
                <?= $item['misi']; ?>
              </td>
              <td>
                <button class="btn btn-info"
                  onclick="edit('<?= $item['id_candidate'] ?>', `<?= $item['name'] ?>`, `<?= htmlspecialchars($item['visi']) ?>`, `<?= htmlspecialchars($item['misi']) ?>`)">Edit</button>
                <a href="/PanitiaPanel/Calon/<?= $item['id_candidate'] ?>" class="btn btn-danger">Hapus</a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="tambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahLabel">Tambah Calon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/PanitiaPanel/Calon" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <label for="name">Nama Calon</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="photo">Foto Potrait Calon</label>
            <input type="file" name="photo" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="visi">Visi</label>
            <textarea name="visi" id="visi" class="form-control summernote" required></textarea>
          </div>

          <div class="mb-3">
            <label for="misi">Misi</label>
            <textarea name="misi" id="misi" class="form-control summernote" required></textarea>
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
        <h5 class="modal-title" id="editLabel">Edit Calon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/PanitiaPanel/Calon/Update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_candidate" id="id_candidate">
        <div class="modal-body">
          <div class="mb-3">
            <label for="name">Nama Calon</label>
            <input type="text" name="name" id="name-edit" class="form-control">
          </div>

          <div class="mb-3">
            <label for="photo">Foto Potrait Calon</label>
            <input type="file" name="photo" class="form-control">
            <small class="text-sm font-weight-bold text-danger">Upload File baru untuk mengganti</small>
          </div>

          <div class="mb-3">
            <label for="visi">Visi</label>
            <textarea name="visi" id="visi-edit" class="form-control summernote"></textarea>
          </div>

          <div class="mb-3">
            <label for="misi">Misi</label>
            <textarea name="misi" id="misi-edit" class="form-control summernote"></textarea>
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
  $('textarea').summernote({
    toolbar: [
      ['font', ['bold', 'underline', 'clear']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['insert', ['link']],
      ['view', ['fullscreen', 'codeview']]
    ],
  });

  const edit = (id, name, visi, misi) => {
    $('#id_candidate').val(id)
    $('#name-edit').val(name)
    $('#visi-edit').summernote('code', visi)
    $('#misi-edit').summernote('code', misi)
    const myModal = new bootstrap.Modal(document.getElementById('edit'));
    myModal.show();
  };
</script>

<?= $this->endSection(); ?>