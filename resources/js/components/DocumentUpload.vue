<template>
  <div class="document-upload-container">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Photo Upload -->
      <div class="upload-section">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          <i class="fas fa-camera mr-2"></i>
          Photo <span class="text-red-500">*</span>
        </label>
        <div class="upload-area" :class="{ 'has-file': documents.photo }">
          <input 
            ref="photoInput"
            type="file" 
            accept="image/*" 
            capture="camera"
            @change="handleFileUpload('photo', $event)"
            class="hidden"
          >
          
          <div v-if="!documents.photo" class="upload-placeholder" @click="openFileDialog('photo')">
            <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
            <p class="text-sm text-gray-600">Click to upload photo</p>
            <p class="text-xs text-gray-500 mt-1">JPG, JPEG, PNG</p>
          </div>
          
          <div v-else class="uploaded-file">
            <img :src="documents.photo.preview" alt="Photo preview" class="w-full h-32 object-cover rounded">
            <div class="file-info">
              <p class="text-xs text-gray-600">{{ documents.photo.name }}</p>
              <div class="flex space-x-2 mt-2">
                <button @click="openFileDialog('photo')" class="text-blue-600 hover:text-blue-800 text-xs">
                  <i class="fas fa-edit mr-1"></i>Change
                </button>
                <button @click="removeFile('photo')" class="text-red-600 hover:text-red-800 text-xs">
                  <i class="fas fa-trash mr-1"></i>Remove
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Aadhar Upload -->
      <div class="upload-section">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          <i class="fas fa-id-card mr-2"></i>
          Aadhar Card <span class="text-red-500">*</span>
        </label>
        <div class="upload-area" :class="{ 'has-file': documents.aadhar }">
          <input 
            ref="aadharInput"
            type="file" 
            accept=".pdf,.jpg,.jpeg,.png" 
            capture="camera"
            @change="handleFileUpload('aadhar', $event)"
            class="hidden"
          >
          
          <div v-if="!documents.aadhar" class="upload-placeholder" @click="openFileDialog('aadhar')">
            <i class="fas fa-id-card text-4xl text-gray-400 mb-2"></i>
            <p class="text-sm text-gray-600">Click to upload Aadhar</p>
            <p class="text-xs text-gray-500 mt-1">PDF, JPG, JPEG, PNG</p>
          </div>
          
          <div v-else class="uploaded-file">
            <div class="file-preview">
              <i class="fas fa-file-pdf text-4xl text-red-500"></i>
            </div>
            <div class="file-info">
              <p class="text-xs text-gray-600">{{ documents.aadhar.name }}</p>
              <div class="flex space-x-2 mt-2">
                <button @click="openFileDialog('aadhar')" class="text-blue-600 hover:text-blue-800 text-xs">
                  <i class="fas fa-edit mr-1"></i>Change
                </button>
                <button @click="removeFile('aadhar')" class="text-red-600 hover:text-red-800 text-xs">
                  <i class="fas fa-trash mr-1"></i>Remove
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Qualification Certificate Upload -->
      <div class="upload-section">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          <i class="fas fa-graduation-cap mr-2"></i>
          Qualification Certificate <span class="text-red-500">*</span>
        </label>
        <div class="upload-area" :class="{ 'has-file': documents.qualification_certificate }">
          <input 
            ref="certInput"
            type="file" 
            accept=".pdf,.jpg,.jpeg,.png" 
            capture="camera"
            @change="handleFileUpload('qualification_certificate', $event)"
            class="hidden"
          >
          
          <div v-if="!documents.qualification_certificate" class="upload-placeholder" @click="openFileDialog('qualification_certificate')">
            <i class="fas fa-graduation-cap text-4xl text-gray-400 mb-2"></i>
            <p class="text-sm text-gray-600">Click to upload certificate</p>
            <p class="text-xs text-gray-500 mt-1">PDF, JPG, JPEG, PNG</p>
          </div>
          
          <div v-else class="uploaded-file">
            <div class="file-preview">
              <i class="fas fa-file-pdf text-4xl text-blue-500"></i>
            </div>
            <div class="file-info">
              <p class="text-xs text-gray-600">{{ documents.qualification_certificate.name }}</p>
              <div class="flex space-x-2 mt-2">
                <button @click="openFileDialog('qualification_certificate')" class="text-blue-600 hover:text-blue-800 text-xs">
                  <i class="fas fa-edit mr-1"></i>Change
                </button>
                <button @click="removeFile('qualification_certificate')" class="text-red-600 hover:text-red-800 text-xs">
                  <i class="fas fa-trash mr-1"></i>Remove
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hidden inputs for form submission -->
    <input type="hidden" name="photo_file" :value="documents.photo ? documents.photo.file : ''">
    <input type="hidden" name="aadhar_file" :value="documents.aadhar ? documents.aadhar.file : ''">
    <input type="hidden" name="qualification_file" :value="documents.qualification_certificate ? documents.qualification_certificate.file : ''">
    <input type="hidden" name="photo_crop_data" :value="documents.photo ? JSON.stringify(documents.photo.cropData) : ''">
  </div>
</template>

<script>
export default {
  name: 'DocumentUpload',
  data() {
    return {
      documents: {
        photo: null,
        aadhar: null,
        qualification_certificate: null
      }
    }
  },
  methods: {
    openFileDialog(type) {
      const inputRef = `${type}Input`;
      this.$refs[inputRef].click();
    },
    
    handleFileUpload(type, event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file type
      if (!this.validateFileType(file, type)) {
        this.showError(`Invalid file type for ${type}`);
        return;
      }

      // Handle photo cropping
      if (type === 'photo') {
        this.handlePhotoUpload(file);
      } else {
        this.handleDocumentUpload(file, type);
      }
    },

    handlePhotoUpload(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        this.documents.photo = {
          file: file,
          name: file.name,
          preview: e.target.result,
          cropData: null
        };
        this.showPhotoCropModal(file);
      };
      reader.readAsDataURL(file);
    },

    handleDocumentUpload(file, type) {
      this.documents[type] = {
        file: file,
        name: file.name,
        type: file.type
      };
      this.showSuccess(`${type} uploaded successfully!`);
    },

    showPhotoCropModal(file) {
      // Create a simple crop interface
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
          <h3 class="text-lg font-semibold mb-4">Crop Photo</h3>
          <div class="mb-4">
            <img id="cropImage" src="${this.documents.photo.preview}" class="max-w-full h-auto rounded" style="max-height: 300px;">
          </div>
          <div class="flex justify-end space-x-2">
            <button id="cancelCrop" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
            <button id="saveCrop" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      document.getElementById('cancelCrop').onclick = () => {
        this.removeFile('photo');
        document.body.removeChild(modal);
      };
      
      document.getElementById('saveCrop').onclick = () => {
        // For now, just save without cropping
        // In a real implementation, you'd integrate with a crop library
        this.documents.photo.cropData = { x: 0, y: 0, width: 100, height: 100 };
        this.showSuccess('Photo uploaded successfully!');
        document.body.removeChild(modal);
      };
    },

    removeFile(type) {
      this.documents[type] = null;
      this.$refs[`${type}Input`].value = '';
    },

    validateFileType(file, type) {
      const allowedTypes = {
        photo: ['image/jpeg', 'image/jpg', 'image/png'],
        aadhar: ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
        qualification_certificate: ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
      };
      
      return allowedTypes[type].includes(file.type);
    },

    showSuccess(message) {
      // Simple success notification
      const notification = document.createElement('div');
      notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
      notification.innerHTML = `<i class="fas fa-check mr-2"></i>${message}`;
      document.body.appendChild(notification);
      setTimeout(() => document.body.removeChild(notification), 3000);
    },

    showError(message) {
      // Simple error notification
      const notification = document.createElement('div');
      notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
      notification.innerHTML = `<i class="fas fa-exclamation mr-2"></i>${message}`;
      document.body.appendChild(notification);
      setTimeout(() => document.body.removeChild(notification), 3000);
    }
  }
}
</script>

<style scoped>
.upload-area {
  @apply border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer transition-colors;
}

.upload-area:hover {
  @apply border-blue-400 bg-blue-50;
}

.upload-area.has-file {
  @apply border-green-400 bg-green-50;
}

.upload-placeholder {
  @apply py-8;
}

.uploaded-file {
  @apply space-y-2;
}

.file-preview {
  @apply flex items-center justify-center h-32 bg-gray-100 rounded;
}

.file-info {
  @apply text-center;
}
</style>
