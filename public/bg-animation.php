<!-- WebGL Canvas for 3D Background -->
<canvas id="webgl-canvas" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none;"></canvas>

<!-- Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<!-- 3D Organic Orbs Animation Script -->
<script>
    (function() {
        const canvas = document.getElementById('webgl-canvas');
        if (!canvas) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // Create Animated Floating Green/Gold Wireframe Orbs
        const geometry = new THREE.IcosahedronGeometry(2, 6);
        const particlesGroup = new THREE.Group();

        for (let i = 0; i < 40; i++) {
            const material = new THREE.MeshBasicMaterial({
                color: i % 2 === 0 ? 0x2d4a36 : 0xd4af37,
                wireframe: true,
                transparent: true,
                opacity: 0.15
            });

            const mesh = new THREE.Mesh(geometry, material);
            mesh.position.x = (Math.random() - 0.5) * 35;
            mesh.position.y = (Math.random() - 0.5) * 35;
            mesh.position.z = (Math.random() - 0.5) * 35;
            mesh.scale.setScalar(Math.random() * 0.8 + 0.2);
            particlesGroup.add(mesh);
        }

        scene.add(particlesGroup);
        camera.position.z = 15;

        // Mouse Parallax Interaction
        let mouseX = 0, mouseY = 0;
        window.addEventListener('mousemove', (e) => {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        });

        // Animation Loop
        function animate() {
            requestAnimationFrame(animate);

            particlesGroup.rotation.y += 0.0015;
            particlesGroup.rotation.x += 0.001;

            camera.position.x += (mouseX * 2 - camera.position.x) * 0.05;
            camera.position.y += (-mouseY * 2 - camera.position.y) * 0.05;

            renderer.render(scene, camera);
        }
        animate();

        // Responsive Resizing
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    })();
</script>