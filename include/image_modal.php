<!-- Image Modal -->
<div id="imgModal" class="image-modal">
    <span class="image-close" onclick="closeImgModal('imgModal')">&times;</span>
    <div class="slidecontainer">
        <input id="rangeSlider" class="slider" oninput="zoomImage(this)" type="range" min="50" max="400" value="100">
    </div>
    <div id="imgCaption"></div>
    <div id="image-modal-wrapper" class="dragscroll">
        <img class="image-modal-content" id="imgModalContent">
    </div>
</div>