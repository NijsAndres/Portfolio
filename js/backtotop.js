let calcScrollValue = () => {
    const scrollProgress = document.querySelector('.c-backtotop');
    let progressValue = document.querySelector('.c-backtotop::before');
    let pos = document.documentElement.scrollTop;
    let calcHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    let scrollValue = Math.round((pos * 100) / calcHeight);
    if (pos > 100) {
        scrollProgress.classList.add('c-backtotop--visible');
    } else {
        scrollProgress.classList.remove('c-backtotop--visible');
    }
    scrollProgress.style.background = `conic-gradient(#e94e35 ${scrollValue}%, #f2f1ef ${scrollValue}%)`;
}

window.onscroll = calcScrollValue;
window.onload = calcScrollValue;
