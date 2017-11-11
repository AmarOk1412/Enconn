<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">

	<title>RT Embedded Sound Localization</title>

	<meta name="description" content="Talk sur la localisation sonore en temps réel sur systèmes embarqués">
	<meta name="author" content="AmarOk">

	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

	<link rel="stylesheet" href="css/reveal.css">
	<link rel="stylesheet" href="css/theme/night.css" id="theme">

	<!-- Theme used for syntax highlighting of code -->
	<link rel="stylesheet" href="lib/css/zenburn.css">

	<!-- Printing and PDF exports -->
	<script>
	var link = document.createElement( 'link' );
	link.rel = 'stylesheet';
	link.type = 'text/css';
	link.href = window.location.search.match( /print-pdf/gi ) ? 'css/print/pdf.css' : 'css/print/paper.css';
	document.getElementsByTagName( 'head' )[0].appendChild( link );
	</script>

	<!--[if lt IE 9]>
	<script src="lib/js/html5shiv.js"></script>
	<![endif]-->
</head>

<body>

	<div class="reveal">

		<!-- Any section element inside of this container is displayed as a slide -->
		<div class="slides">
			<section>
				<h1>RT Embedded Sound Localization</h1>
				<h3>ou comment localiser un son avec peu de ressources et en temps réel.</h3>
				<p>
					<small>Created by <a href="mailto://amarok@enconn.fr">AmarOk</a> / <a href="https://twitter.com/AmarOk1412">@AmarOk1412</a> / <a href="https://enconn.fr">enconn.fr</a></small>
				</p>
			</section>


			<section>
				<section>
					<h2>Qui suis-je ?</h2>
					<ul>
						<li>Étudiant ESIR/UQAC</li>
						<li>Contributeur (Logiciel libre, hackerspaces, zestedesavoir, etc)</li>
						<li>Embarqué, Machine Learning</li>
					</ul>
				</section>

				<section>
					<h2>But du talk</h2>
					<ul>
						<li>Présentation d'un de mes projets</li>
						<li>Comprendre comment la localisation avec des microphones fonctionne</li>
						<li>Réaliser des optimisations pour un système embarqué</li>
					</ul>
				</section>

				<section data-background="img/go.gif">
				</section>

			</section>

			<section>
				<section>
					<h2>L'algorithme</h2>
				</section>
				<section>
					<h2>1. Récupérer un signal</h2>
					<p>
						Plusieurs possibilités : <br>
						<ul>
							<li>Si on souhaite travailler sur du temps différé il faut un fichier sonore (un wav sur de multiples channels) et le stocker dans une structure type un vecteur de vecteurs (un vecteur = un signal).</li>
							<li>Si on souhaite faire du temps réel, il faut traiter les données brutes (format type PCM 16 bits 48 KHz, X channels entrelacés) et stocker dans une structure type vecteur de deque.</li>
						</ul>
					</p>
				</section>
				<section>
					<h2>2. Choisir les meilleurs signaux</h2>
					La partie suivante se fait avec 2 signaux. On doit donc choisir 2 signaux parmi les X qu'on a à disposition.<br>
					<ul>
						<li>Soit on prend les micros les plus éloignés entre eux (ce qui permet d'avoir une plus grande précision dans l'angle)</li>
						<li>Soit on prend les 2 micros les plus proches de la source sonore (on obtient donc déjà une direction grossière, par exemple avec 4 micros Nord, Sud, Est, Ouest). Ce calcul se fait via une cross-corrélation et on prend les deux les plus en avant.</li>
					</ul>
				</section>
				<section>
					<h2>3. Calculer l'angle de provenance</h2>
					Une fois les 2 micros choisis, on peut calculer deux angles de provenance à 180°.<br>
					<img width="60%" data-src="img/angle.png" alt="Angle">
				</section>
				<section>
					<h2>4. répéter pour les X dimensions</h2>
				</section>
				<section>
					<h2>5. Éliminer le bruit</h2>
					Prendre en compte plusieurs valeurs et ainsi supprimer les erreurs de détections et obtenir un smoothing "naturel".<br>
					On peut pondérer les différentes valeurs différement (de manière logarithmique, linéaire, etc).
				</section>
				<section  data-background="img/yeah.gif">
				</section>
			</section>

			<section>
				<section>
					<h2>Portage sur de petites architectures</h2>
					<p>
					</p>
				</section>
				<section data-background="img/problem.gif">
				</section>
				<section>
					<h2>Les limites de l'embarqué</h2>
					<p>
					<ul>
						<li>Notre code en C++14/Rust n'est pas compatible. On doit refaire en C99 (voir embedded c++, yeark!)</li>
						<li>malloc ? Trop long. acos ? Trop long. qsort ? Trop long.</li>
						<li>RAM capacity</li>
					</ul>
					</p>
				</section>
				<section>
					<h2>Des solutions</h2>
					<p>
					<ul>
						<li>C99 ou changer de carte (Rpi3, BBB, etc. supportent le C++14 ou Rust)</li>
						<li>malloc ? Allocation au démarrage. Programme fixe. </li>
						<li>acos ? sauvegarder en mémoire. Si il y a des limites de puissance, préfèrons la mémoire.</li>
						<li>qsort ? Optimiser dans son cas.</li>
						<li>RAM capacity => Utiliser des buffers limites (ici 0.13 secondes suffisent)</li>
					</ul>
					</p>
				</section>
			</section>



			<section>
				<section>
					<h2>Le projet au final</h2>
					<p>Localisation fonctionnelle sur des environnements linux + sur des cartes STM32F4XX (STM32F401 testée).<br>
					<img width="40%" data-src="img/all.jpg" alt="All">
					</p>
				</section>
				<section data-background="img/question.gif">
				</section>
			</section>

		</div>

	</div>

	<script src="lib/js/head.min.js"></script>
	<script src="js/reveal.js"></script>

	<script>

	// More info https://github.com/hakimel/reveal.js#configuration
	Reveal.initialize({
		controls: true,
		progress: true,
		history: true,
		center: true,

		transition: 'slide', // none/fade/slide/convex/concave/zoom

		// More info https://github.com/hakimel/reveal.js#dependencies
		dependencies: [
			{ src: 'lib/js/classList.js', condition: function() { return !document.body.classList; } },
			{ src: 'plugin/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
			{ src: 'plugin/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
			{ src: 'plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } },
			{ src: 'plugin/zoom-js/zoom.js', async: true },
			{ src: 'plugin/notes/notes.js', async: true }
		]
	});

	</script>

</body>
</html>
