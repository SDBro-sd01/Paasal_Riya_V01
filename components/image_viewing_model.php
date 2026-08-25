<!-- Modern Dark Image Viewer Modal - Fully Scoped & Independent Styles -->
<style>
  /* Backdrop style ONLY when image viewer modal is shown */
  #imageViewerModal.show ~ .modal-backdrop.show {
    backdrop-filter: blur(8px) !important;
    background-color: rgba(0, 0, 0, 0.8) !important;
  }

  /* Modal content - glassmorphism dark theme, scoped with #imageViewerModal */
  #imageViewerModal .modal-content {
    background: linear-gradient(145deg, #1a1a2e 0%, #16213e 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7), 0 0 40px rgba(123, 97, 255, 0.15) !important;
    overflow: hidden !important;
  }

  /* Close button */
  #imageViewerModal .btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
    opacity: 0.7;
    transition: all 0.3s ease;
    transform: scale(1.2);
  }
  #imageViewerModal .btn-close-white:hover {
    opacity: 1;
    transform: scale(1.4) rotate(90deg);
  }

  /* Modal body */
  #imageViewerModal .modal-body {
    padding: 1.5rem 1.5rem 1rem !important;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* The image */
  #imageViewerModal #viewerImage {
    max-width: 100% !important;
    max-height: 70vh !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), 0 0 15px rgba(123, 97, 255, 0.2) !important;
    object-fit: contain !important;
    transition: opacity 0.3s ease;
    opacity: 1;
    background: #0f0f1a !important; /* placeholder while loading */
  }
  #imageViewerModal #viewerImage.fade-out {
    opacity: 0 !important;
  }

  /* Navigation buttons */
  #imageViewerModal .img-nav-btn {
    background: transparent !important;
    border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
    color: white !important;
    border-radius: 50px !important;
    padding: 10px 28px !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    transition: all 0.25s ease;
    backdrop-filter: blur(4px);
    margin: 0 8px;
    letter-spacing: 0.5px;
  }
  #imageViewerModal .img-nav-btn:hover {
    background: rgba(123, 97, 255, 0.25) !important;
    border-color: #bb86fc !important;
    box-shadow: 0 0 20px rgba(187, 134, 252, 0.5) !important;
    transform: translateY(-2px);
    color: #ffffff !important;
  }
  #imageViewerModal .img-nav-btn i {
    margin: 0 5px;
  }

  /* Image counter */
  #imageViewerModal #imageCounter {
    color: rgba(255, 255, 255, 0.7) !important;
    font-size: 0.9rem !important;
    margin-top: 10px;
    font-weight: 500 !important;
    background: rgba(255, 255, 255, 0.05) !important;
    padding: 6px 18px !important;
    border-radius: 30px !important;
    backdrop-filter: blur(5px);
  }
</style>

<!-- Modal HTML (unchanged structure) -->
<div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-0">
        <img id="viewerImage" src="" alt="Service Image">
        <div class="mt-3 d-flex align-items-center justify-content-center flex-wrap">
          <button class="img-nav-btn" id="prevImageBtn"><i class="fas fa-chevron-left"></i> <?php echo __t('prev', 'Prev'); ?></button>
          <span id="imageCounter" class="mx-3">1 / 1</span>
          <button class="img-nav-btn" id="nextImageBtn"><?php echo __t('next', 'Next'); ?> <i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  // Prevent duplicate initialization if this component is included multiple times
  if (window._imageViewerInitialized) return;
  window._imageViewerInitialized = true;

  // Bootstrap modal instance
  let imageViewerModal = new bootstrap.Modal(document.getElementById('imageViewerModal'));

  // Global state for gallery
  window.currentImageList = [];
  window.currentImageIndex = 0;

  // Function to open gallery (exposed globally)
  window.openImageGallery = function(serviceId) {
    fetch(`includes/fetch_services.php?service_id=${serviceId}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.services[0].images && data.services[0].images.length) {
          window.currentImageList = data.services[0].images;
          window.currentImageIndex = 0;
          showImageViewer();
          imageViewerModal.show();
        } else {
          alert((window.__indexTrans && window.__indexTrans.no_images) || 'No images available');
        }
      })
      .catch(() => {
        alert('Failed to load images. Please try again.');
      });
  };

  // Smooth fade transition
  function showImageViewer() {
    const img = document.getElementById('viewerImage');
    const counter = document.getElementById('imageCounter');
    if (window.currentImageList && window.currentImageList.length > 0) {
      img.classList.add('fade-out');
      setTimeout(() => {
        img.src = window.currentImageList[window.currentImageIndex].image_path;
        img.onload = () => img.classList.remove('fade-out');
        // Fallback for cached images
        setTimeout(() => img.classList.remove('fade-out'), 50);
      }, 200);

      if (counter) {
        counter.textContent = `${window.currentImageIndex + 1} / ${window.currentImageList.length}`;
      }
    }
  }

  // Navigation buttons
  document.getElementById('prevImageBtn').addEventListener('click', function() {
    if (window.currentImageList && window.currentImageIndex > 0) {
      window.currentImageIndex--;
      showImageViewer();
    }
  });

  document.getElementById('nextImageBtn').addEventListener('click', function() {
    if (window.currentImageList && window.currentImageIndex < window.currentImageList.length - 1) {
      window.currentImageIndex++;
      showImageViewer();
    }
  });

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    if (document.body.classList.contains('modal-open') && document.getElementById('imageViewerModal').classList.contains('show')) {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        if (window.currentImageList && window.currentImageIndex > 0) {
          window.currentImageIndex--;
          showImageViewer();
        }
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        if (window.currentImageList && window.currentImageIndex < window.currentImageList.length - 1) {
          window.currentImageIndex++;
          showImageViewer();
        }
      }
    }
  });

  // Reset when modal is hidden
  document.getElementById('imageViewerModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('viewerImage').src = '';
  });
})();
</script>