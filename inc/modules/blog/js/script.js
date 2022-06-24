if (window.Shuffle) {
	window.posts_shuffleInstance = null;

	jQuery(document).ready(function() {
		var Shuffle = window.Shuffle;
		var element = document.querySelector(
			".posts-layout.layout--blog_masonry"
		);
		if (element) {
			window.posts_shuffleInstance = new Shuffle(element, {
				itemSelector: "article"
			});
		}

		// tophive_blog_posts
		jQuery(document).on("selective-refresh-content-rendered", function(
			e,
			id
		) {
			try {
				if (id === "tophive_blog_posts") {
					if (window.posts_shuffleInstance) {
						window.posts_shuffleInstance.destroy();
						window.posts_shuffleInstance = null;
					}
					var Shuffle = window.Shuffle;
					var element = document.querySelector(
						".posts-layout.layout--blog_masonry"
					);
					if (element) {
						window.posts_shuffleInstance = new Shuffle(element, {
							itemSelector: "article"
						});
					}
				}
			} catch( e ){
				
			}
		});
		jQuery(document).on("after_auto_render_css", function(e, id) {
			if (window.posts_shuffleInstance) {
				window.posts_shuffleInstance.update();
			}
		});
	});
}
