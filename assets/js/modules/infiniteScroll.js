import { setupLazyLoading } from './lazyLoad.js';

export function setupInfiniteScroll($) {
    const postsContainer = document.querySelector('.site-main');
    if (!postsContainer) {
        return;
    }

    if (typeof gp_settings === 'undefined' || !gp_settings.ajax_url || !gp_settings.load_more_posts_nonce) {
        return;
    }

    let currentPage = 1;
    let isLoading = false;
    let noMorePosts = false;

    function loadPosts(pageToLoad) {
        if (isLoading || noMorePosts) return;

        isLoading = true;
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.textContent = 'Loading...';
            loadMoreBtn.disabled = true;
        }

        $.ajax({
            url: gp_settings.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_posts',
                page: pageToLoad,
                nonce: gp_settings.load_more_posts_nonce
            },
            success: function(response) {
                isLoading = false;
                const currentButtonContainer = document.querySelector('.load-more-container');
                if (currentButtonContainer) {
                    currentButtonContainer.remove();
                }

                if (response.success && response.data && response.data.html && typeof response.data.html === 'string' && response.data.html.trim() !== '') {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = response.data.html;
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = response.data.html;

                    const newPosts = Array.from(tempContainer.children);
                    newPosts.forEach(post => {
                        postsContainer.appendChild(post);
                        // ★★★ 바로 이 위치에 아래 코드를 추가합니다. ★★★
// 페이지 로드 시 인피드 광고 카운터 초기화
if (typeof window.infeedAdCount === 'undefined') {
    window.infeedAdCount = 0;
}
// AJAX로 로드된 포스트 카운터
if (typeof window.ajaxPostCount === 'undefined') {
    window.ajaxPostCount = 0;
}
window.ajaxPostCount++;

// 4개의 포스트마다, 그리고 총 광고 수가 3개 미만일 때만 인피드 광고 삽입
if (window.ajaxPostCount > 0 && window.ajaxPostCount % 4 === 0 && window.infeedAdCount < 3 && gp_settings.ad_client && gp_settings.infeed_ad_slot) {
    const adArticle = document.createElement('article');
    adArticle.className = 'post type-post status-publish format-standard hentry manual-ad-article';
    adArticle.innerHTML = `<div class="inside-article ad-container"><div class="manual-ad-container in-feed-ad"><ins class="adsbygoogle" style="display:block" data-ad-format="fluid" data-ad-layout-key="-fb+5w+4e-db+86" data-ad-client="${gp_settings.ad_client}" data-ad-slot="${gp_settings.infeed_ad_slot}"></ins></div></div>`;

    // 삽입된 광고 수를 추적
    window.infeedAdCount++;

    const lastPost = postsContainer.lastElementChild;
    if (lastPost) {
        postsContainer.insertBefore(adArticle, lastPost.nextSibling);
    } else {
        postsContainer.appendChild(adArticle);
    }
}
                    });

                    if (typeof setupLazyLoading === 'function') {
                        setTimeout(() => setupLazyLoading(postsContainer), 0);
                    }
                    // AJAX로 새 포스트 로드 후 광고 초기화 함수 호출
                    if (typeof initializeAds === 'function') {
                        initializeAds();
                    }
                }

                if (response.success && response.data && response.data.button_html && typeof response.data.button_html === 'string' && response.data.button_html.trim() !== '') {
                    const newButtonTempDiv = document.createElement('div');
                    newButtonTempDiv.innerHTML = response.data.button_html;
                    postsContainer.appendChild(newButtonTempDiv.firstChild);
                    addLoadMoreButtonListener();
                } else {
                    noMorePosts = true;
                    if (!document.getElementById('no-more-posts-message')) {
                        const noMoreMessage = document.createElement('p');
                        noMoreMessage.id = 'no-more-posts-message';
                        noMoreMessage.textContent = 'This is the last page.';
                        noMoreMessage.style.textAlign = 'center';
                        postsContainer.appendChild(noMoreMessage);
                    }
                }
                currentPage = pageToLoad;
            },
            error: function(jqXHR, textStatus, errorThrown) {
                isLoading = false;
                const btn = document.getElementById('load-more-btn');
                if(btn) {
                    btn.textContent = 'Error. Try Again?';
                    btn.disabled = false;
                }
            }
        });
    }

    function addLoadMoreButtonListener() {
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.removeEventListener('click', handleLoadMoreClick);
            loadMoreBtn.addEventListener('click', handleLoadMoreClick);
        }
    }

    function handleLoadMoreClick() {
        loadPosts(currentPage + 1);
    }

    if (gp_settings.isFrontPage || gp_settings.isHome) {
        $.ajax({
            url: gp_settings.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_posts',
                page: 1,
                nonce: gp_settings.load_more_posts_nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    if (response.data.button_html && typeof response.data.button_html === 'string' && response.data.button_html.trim() !== '') {
                        const newButtonTempDiv = document.createElement('div');
                        newButtonTempDiv.innerHTML = response.data.button_html;
                        postsContainer.appendChild(newButtonTempDiv.firstChild);
                        addLoadMoreButtonListener();
                    } else if ((!response.data.html || (typeof response.data.html === 'string' && response.data.html.trim() === ''))) {
                        noMorePosts = true;
                        if (!document.getElementById('no-more-posts-message')) {
                            const noMoreMessage = document.createElement('p');
                            noMoreMessage.id = 'no-more-posts-message';
                            noMoreMessage.textContent = 'No posts found.';
                            noMoreMessage.style.textAlign = 'center';
                            postsContainer.appendChild(noMoreMessage);
                        }
                    } else if (response.data.html && typeof response.data.html === 'string' && response.data.html.trim() !== '' &&
                               (!response.data.button_html || (typeof response.data.button_html === 'string' && response.data.button_html.trim() === ''))) {
                        noMorePosts = true;
                        if (!document.getElementById('no-more-posts-message')) {
                            const noMoreMessage = document.createElement('p');
                            noMoreMessage.id = 'no-more-posts-message';
                            noMoreMessage.textContent = 'This is the last page.';
                            noMoreMessage.style.textAlign = 'center';
                            postsContainer.appendChild(noMoreMessage);
                        }
                    }
                } else {
                     if (!document.getElementById('no-more-posts-message')) {
                        const errorMessage = document.createElement('p');
                        errorMessage.id = 'no-more-posts-message';
                        errorMessage.textContent = 'Could not load posts. Please try again later.';
                        errorMessage.style.textAlign = 'center';
                        postsContainer.appendChild(errorMessage);
                    }
                }
            },
            error: function() {
                 if (!document.getElementById('no-more-posts-message')) {
                    const errorMessage = document.createElement('p');
                    errorMessage.id = 'no-more-posts-message';
                    errorMessage.textContent = 'Error connecting to server. Please try again later.';
                    errorMessage.style.textAlign = 'center';
                    postsContainer.appendChild(errorMessage);
                }
            }
        });
    } else {
        if (!document.body.classList.contains('single-post') &&
            !(typeof gp_settings !== 'undefined' && gp_settings.currentPostType === 'page')) {

            const loaderElement = document.createElement('div');
            loaderElement.id = 'infinite-scroll-loader';
            loaderElement.className = 'infinite-scroll-loader';
            loaderElement.style.height = '1px';
            loaderElement.style.opacity = '0';
            postsContainer.appendChild(loaderElement);

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !isLoading && !noMorePosts) {
                        loadPosts(currentPage + 1);
                    }
                });
            }, { rootMargin: '0px 0px 300px 0px', threshold: 0.01 }
            );

            if (loaderElement) {
                observer.observe(loaderElement);
            }
        }
    }
}

export function setupSeriesLoadMoreButton($) {
    const seriesContainer = document.querySelector('.gp-series-posts-container');
    if (!seriesContainer) {
        return;
    }

    const loadMoreBtn = document.getElementById('load-more-series-btn');
    if (!loadMoreBtn) {
        return;
    }

    const seriesGrid = seriesContainer.querySelector('.series-posts-grid');
    if (!seriesGrid) {
        return;
    }

    let currentPostId = seriesContainer.dataset.currentPostId;
    let initialPostsCount = parseInt(seriesContainer.dataset.initialPostsCount, 10);
    let postsPerLoad = parseInt(seriesContainer.dataset.loadMoreCount, 10);
    let maxClicks = parseInt(seriesContainer.dataset.maxClicks, 10);
    let totalRelatedPosts = parseInt(seriesContainer.dataset.totalRelatedPosts, 10);

    let currentOffset = initialPostsCount;
    let clickCount = 0;
    let isLoading = false;

    loadMoreBtn.addEventListener('click', function() {
        if (isLoading || clickCount >= maxClicks) {
            return;
        }

        isLoading = true;
        loadMoreBtn.textContent = 'Loading...';
        loadMoreBtn.disabled = true;

        $.ajax({
            url: gp_settings.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_series_posts',
                nonce: gp_settings.load_more_series_nonce,
                current_post_id: currentPostId,
                offset: currentOffset,
                posts_per_page: postsPerLoad,
                initial_posts_count: initialPostsCount
            },
            success: function(response) {
                isLoading = false;
                if (response.success && response.data.html) {
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = response.data.html;

                    const newPosts = Array.from(tempContainer.children);
                    newPosts.forEach(post => {
                        seriesGrid.appendChild(post);
                    });

                    if (typeof setupLazyLoading === 'function') {
                        setTimeout(() => setupLazyLoading(seriesGrid), 0);
                    }

                    currentOffset = response.data.new_offset;
                    clickCount++;

                    if (!response.data.has_more || clickCount >= maxClicks || currentOffset >= totalRelatedPosts) {
                        loadMoreBtn.textContent = 'No More Series';
                        loadMoreBtn.disabled = true;
                        setTimeout(() => {
                            loadMoreBtn.style.display = 'none';
                        }, 2000);
                    } else {
                        loadMoreBtn.textContent = 'Series More';
                        loadMoreBtn.disabled = false;
                    }
                } else {
                    loadMoreBtn.textContent = response.data.message || 'No More Series';
                    loadMoreBtn.disabled = true;
                    setTimeout(() => {
                        loadMoreBtn.style.display = 'none';
                    }, 2000);
                }
            },
            error: function() {
                isLoading = false;
                loadMoreBtn.textContent = 'Error. Try Again?';
                loadMoreBtn.disabled = false;
            }
        });
    });
}
