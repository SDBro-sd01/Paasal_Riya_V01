<!-- Rating Modal Component -->
<style>
    #ratingModelCompModal .modal-content {
        background: #1a1a24;
        border: 1px solid #2a2a38;
        border-radius: 20px;
        color: #e2e8f0;
    }
    #ratingModelCompModal .modal-header {
        background: linear-gradient(135deg, #2d2200 0%, #4a3800 100%);
        border-bottom: 1px solid #3a3000;
        padding: 1.2rem 1.5rem 0.8rem;
    }
    #ratingModelCompModal .modal-header h5 {
        font-weight: 700;
        color: #fbbf24;
    }
    #ratingModelCompModal .modal-body {
        padding: 2rem;
    }
    #ratingModelCompModal .btn-warning {
        background: #d97706;
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 0.6rem 2rem;
        border-radius: 50px;
    }
    #ratingModelCompModal .btn-secondary {
        background: transparent;
        border: 1px solid #2a2a38;
        color: #a0aec0;
        border-radius: 50px;
        padding: 0.6rem 2rem;
    }
    .modern-stars-comp {
        display: flex;
        justify-content: center;
        gap: 12px;
        font-size: 2.2rem;
        direction: rtl;
        unicode-bidi: bidi-override;
    }
    .modern-stars-comp input[type="radio"] {
        display: none;
    }
    .modern-stars-comp label {
        color: #2a2a38;
        cursor: pointer;
        transition: 0.2s;
    }
    .modern-stars-comp label:hover,
    .modern-stars-comp label:hover ~ label,
    .modern-stars-comp input[type="radio"]:checked ~ label {
        color: #f59e0b;
        transform: scale(1.15);
    }
    .rating-emoji-comp {
        font-size: 3rem;
        transition: 0.3s;
    }
    .rating-value-text-comp {
        font-weight: 600;
        color: #a0aec0;
    }
</style>

<!-- Modal HTML -->
<div class="modal fade" id="ratingModelCompModal" tabindex="-1" aria-labelledby="ratingModelTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-star me-2"></i>Rate this Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="ratingServiceNameComp" class="fw-bold mb-4" style="font-size:1.1rem;"></p>
                <div class="modern-stars-comp" id="modernStarRatingComp">
                    <input type="radio" name="ratingComp" value="5" id="star5Comp"><label for="star5Comp">★</label>
                    <input type="radio" name="ratingComp" value="4" id="star4Comp"><label for="star4Comp">★</label>
                    <input type="radio" name="ratingComp" value="3" id="star3Comp"><label for="star3Comp">★</label>
                    <input type="radio" name="ratingComp" value="2" id="star2Comp"><label for="star2Comp">★</label>
                    <input type="radio" name="ratingComp" value="1" id="star1Comp"><label for="star1Comp">★</label>
                </div>
                <div class="mt-3 rating-emoji-comp" id="ratingEmojiComp">😶</div>
                <div class="rating-value-text-comp" id="ratingTextComp">Tap a star</div>
                <input type="hidden" id="currentServiceIdComp">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-warning" id="submitRatingBtnComp"><i class="fas fa-paper-plane me-2"></i>Submit Rating</button>
            </div>
        </div>
    </div>
</div>

<script>
    let ratingModelCompModal;
    const emojisComp = {1:'😠',2:'😕',3:'😐',4:'😊',5:'😍'};
    const textsComp = {1:'Poor',2:'Fair',3:'Good',4:'Very Good',5:'Excellent!'};

    document.addEventListener('DOMContentLoaded', function() {
        ratingModelCompModal = new bootstrap.Modal(document.getElementById('ratingModelCompModal'));

        // Star rating interaction
        const stars = document.querySelectorAll('#modernStarRatingComp input');
        const emojiEl = document.getElementById('ratingEmojiComp');
        const textEl = document.getElementById('ratingTextComp');
        stars.forEach(star => {
            star.addEventListener('change', function() {
                const val = this.value;
                emojiEl.textContent = emojisComp[val];
                textEl.textContent = textsComp[val];
            });
        });

        // Submit rating handler
        document.getElementById('submitRatingBtnComp').addEventListener('click', function() {
            const val = document.querySelector('#modernStarRatingComp input[name="ratingComp"]:checked')?.value;
            if (!val) {
                alert('Please select a rating');
                return;
            }
            const serviceId = document.getElementById('currentServiceIdComp').value;
            const fd = new FormData();
            fd.append('service_id', serviceId);
            fd.append('rating', val);
            fetch('includes/submit_rating.php', {method: 'POST', body: fd})
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    ratingModelCompModal.hide();
                    if (typeof loadServices === 'function') {
                        loadServices(); // refresh the service list
                    }
                } else {
                    alert(data.message || 'Rating submission failed');
                }
            })
            .catch(() => alert('Network error'));
        });
    });

    /**
     * Open the rating modal.
     * @param {number} serviceId
     * @param {string} serviceName
     * @param {number} currentRating - user's existing rating (0 if none)
     */
    function openRatingModelComp(serviceId, serviceName, currentRating) {
        document.getElementById('currentServiceIdComp').value = serviceId;
        document.getElementById('ratingServiceNameComp').textContent = serviceName;
        const stars = document.querySelectorAll('#modernStarRatingComp input');
        stars.forEach(r => r.checked = false);
        document.getElementById('ratingEmojiComp').textContent = '😶';
        document.getElementById('ratingTextComp').textContent = 'Tap a star';

        if (currentRating && currentRating > 0) {
            const starToCheck = document.getElementById('star' + currentRating + 'Comp');
            if (starToCheck) {
                starToCheck.checked = true;
                document.getElementById('ratingEmojiComp').textContent = emojisComp[currentRating];
                document.getElementById('ratingTextComp').textContent = textsComp[currentRating];
            }
        }
        ratingModelCompModal.show();
    }
</script>