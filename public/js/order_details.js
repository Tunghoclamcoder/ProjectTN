function openReviewModal() {
    document.getElementById('reviewModal').style.display = 'block';
}
function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}
// Đóng modal khi click ra ngoài
window.onclick = function(event) {
    var modal = document.getElementById('reviewModal');
    if (event.target == modal) {
        closeReviewModal();
    }
}
