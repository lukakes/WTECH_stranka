<script>
  const uploadInput = document.getElementById('images');
  const uploadBox = document.getElementById('admin-upload-box');
  const previewGrid = document.getElementById('admin-upload-preview');
  let selectedUploadFiles = [];

  function syncUploadInput() {
    if (!uploadInput) {
      return;
    }

    const dataTransfer = new DataTransfer();
    selectedUploadFiles.forEach((file) => dataTransfer.items.add(file));
    uploadInput.files = dataTransfer.files;
  }

  function addUploadFiles(files) {
    Array.from(files).forEach((file) => {
      if (!file.type.startsWith('image/')) {
        return;
      }

      const duplicate = selectedUploadFiles.some((selectedFile) => (
        selectedFile.name === file.name
        && selectedFile.size === file.size
        && selectedFile.lastModified === file.lastModified
      ));

      if (!duplicate) {
        selectedUploadFiles.push(file);
      }
    });

    syncUploadInput();
    renderImagePreviews();
  }

  function renderImagePreviews() {
    if (!previewGrid) {
      return;
    }

    previewGrid.innerHTML = '';

    selectedUploadFiles.forEach((file, index) => {
      const reader = new FileReader();
      reader.addEventListener('load', () => {
        const preview = document.createElement('div');
        preview.className = 'admin-upload-preview-item';
        preview.innerHTML = `
          <button type="button" aria-label="Remove ${file.name}">
            <i class="fa-solid fa-xmark"></i>
          </button>
          <img src="${reader.result}" alt="">
          <span>${file.name}</span>
        `;

        preview.querySelector('button').addEventListener('click', () => {
          selectedUploadFiles.splice(index, 1);
          syncUploadInput();
          renderImagePreviews();
        });

        previewGrid.appendChild(preview);
      });
      reader.readAsDataURL(file);
    });
  }

  if (uploadInput && uploadBox) {
    uploadInput.addEventListener('change', () => {
      addUploadFiles(uploadInput.files);
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
      uploadBox.addEventListener(eventName, (event) => {
        event.preventDefault();
        uploadBox.classList.add('is-dragging');
      });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
      uploadBox.addEventListener(eventName, (event) => {
        event.preventDefault();
        uploadBox.classList.remove('is-dragging');
      });
    });

    uploadBox.addEventListener('drop', (event) => {
      if (!event.dataTransfer?.files?.length) {
        return;
      }

      addUploadFiles(event.dataTransfer.files);
    });
  }
</script>
