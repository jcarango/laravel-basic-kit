<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DEMOSOL - Colombia</title>

    <!-- Fonts: Instrument Sans for high readability -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Fallback Tailwind CDN in case Vite build fails for user -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: '#d97706', // Amber 600 - High Contrast
                        secondary: '#0f172a', // Slate 900 - High Contrast
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        /* High accessibility focus styles */
        a:focus,
        button:focus {
            outline: 3px solid #d97706;
            outline-offset: 2px;
        }
    </style>
</head>

<body class="antialiased bg-white text-slate-900 selection:bg-amber-200 selection:text-slate-900">

    <!-- Navbar Minimalist -->
    <nav class="fixed w-full z-50 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <img src="{{ asset('storage/logodemosol.jpeg') }}" alt="DEMOSOL Logo" class="h-20 w-auto">
                </div>

                <!-- Desktop Menu -->
                <div class="hidden xl:flex space-x-8 text-sm font-medium text-slate-600">
                    <a href="#nosotros"
                        class="hover:text-amber-700 hover:underline underline-offset-4 transition-all">NOSOTROS</a>
                    <a href="#ecosistema"
                        class="hover:text-amber-700 hover:underline underline-offset-4 transition-all">ECOSISTEMA</a>
                    <a href="#formacion"
                        class="hover:text-amber-700 hover:underline underline-offset-4 transition-all">FORMACIÓN</a>
                    <a href="#propuestas"
                        class="hover:text-amber-700 hover:underline underline-offset-4 transition-all">PROPUESTAS</a>
                    <a href="#transparencia"
                        class="hover:text-amber-700 hover:underline underline-offset-4 transition-all">TRANSPARENCIA</a>
                </div>

                <!-- CTA -->
                <div class="flex items-center gap-2">
                    <a href="/admin"
                        class="px-4 py-2 bg-slate-900 text-white font-medium text-xs sm:text-sm rounded hover:bg-slate-800 transition-colors">
                        Ingresar
                    </a>
                    <a href="/hoja-de-vida"
                        class="hidden sm:block px-4 py-2 bg-amber-600 text-white font-medium text-xs sm:text-sm rounded hover:bg-amber-700 transition-colors shadow-sm">
                        Hoja de Vida
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Minimal -->
    <header class="pt-32 pb-20 md:pt-40 md:pb-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl">
            <div
                class="inline-block px-3 py-1 mb-6 text-xs font-bold tracking-wider text-amber-700 uppercase bg-amber-50 rounded-full border border-amber-100">
                Democracia Solidaria
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-slate-900 tracking-tight leading-tight mb-8">
                Liderazgo que <br>
                <span class="text-amber-600">ilumina, no oculta.</span>
            </h1>
            <p class="text-xl text-slate-600 leading-relaxed max-w-2xl mb-10">
                Somos una muestra de luz. Integramos la sabiduría del territorio con la innovación global para construir
                un paraíso de oportunidades basado en la ética y la transparencia.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/admin"
                    class="px-8 py-4 bg-slate-900 text-white font-bold rounded hover:bg-slate-800 transition-all text-center flex items-center justify-center gap-2 group">
                    <span>Ingresar al Portal</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </a>
                <a href="#formacion"
                    class="px-8 py-4 bg-amber-600 text-white font-bold rounded hover:bg-amber-700 transition-colors text-center">
                    Quiero ser Socio del Cambio
                </a>
                <a href="#ecosistema"
                    class="px-8 py-4 bg-white text-slate-700 font-bold border border-slate-300 rounded hover:bg-slate-50 transition-colors text-center">
                    Ver Ecosistema
                </a>
                <!-- Botón para Testigos -->
                <a href="/admin/login"
                    class="px-8 py-4 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-700 transition-all text-center flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 3v8m0 0l-8.944-8.944M14 6h4M14 10h4M14 14h4" />
                    </svg>
                    <span>Ingreso Testigos</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Us Section (Clean Grid) -->
    <section id="nosotros" class="py-24 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-6">Nuestra Historia</h2>
                    <p class="text-lg text-slate-600 leading-relaxed mb-6">
                        DEMOSOL nace en el corazón de la crisis del 2020. Un grupo de líderes territoriales y
                        profesionales decidimos dejar de ser espectadores.
                    </p>
                    <p class="text-lg text-slate-600 leading-relaxed">
                        Unimos la lucha histórica de la Ley 70 con modelos de gestión de alta eficiencia. No somos
                        políticos tradicionales; somos ciudadanos construyendo lo que nos merecemos.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-6 bg-white border border-slate-200 rounded">
                        <div class="text-3xl mb-2">🤝</div>
                        <h3 class="font-bold text-slate-900 mb-1">Integridad</h3>
                        <p class="text-sm text-slate-500">Actuar con honestidad.</p>
                    </div>
                    <div class="p-6 bg-white border border-slate-200 rounded">
                        <div class="text-3xl mb-2">⚖️</div>
                        <h3 class="font-bold text-slate-900 mb-1">Meritocracia</h3>
                        <p class="text-sm text-slate-500">Sin roscas, solo talento.</p>
                    </div>
                    <div class="p-6 bg-white border border-slate-200 rounded">
                        <div class="text-3xl mb-2">🔍</div>
                        <h3 class="font-bold text-slate-900 mb-1">Transparencia</h3>
                        <p class="text-sm text-slate-500">Cuentas claras siempre.</p>
                    </div>
                    <div class="p-6 bg-white border border-slate-200 rounded">
                        <div class="text-3xl mb-2">🌿</div>
                        <h3 class="font-bold text-slate-900 mb-1">Territorio</h3>
                        <p class="text-sm text-slate-500">Respeto por la raíz.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ecosystem (Tabs/Cards Minimal) -->
    <section id="ecosistema" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Ecosistema Global</h2>
                <div class="w-16 h-1 bg-amber-600 mt-4"></div>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="p-6 border-l-4 border-amber-500 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Modelo Global</h3>
                    <p class="text-slate-600 text-sm">Adaptamos la eficiencia de Singapur y Kaizen a nuestros barrios.
                        Mejora continua real.</p>
                </div>
                <div class="p-6 border-l-4 border-teal-500 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Justicia Territorial</h3>
                    <p class="text-slate-600 text-sm">Protección absoluta de la Ley 70 y la propiedad colectiva de
                        nuestras comunidades.</p>
                </div>
                <div class="p-6 border-l-4 border-blue-500 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Bio-Tecnología</h3>
                    <p class="text-slate-600 text-sm">Herramientas digitales para monitorear y potenciar nuestros
                        recursos naturales.</p>
                </div>
                <div class="p-6 border-l-4 border-slate-800 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Meritocracia</h3>
                    <p class="text-slate-600 text-sm">El liderazgo se gana estudiando. Certificación obligatoria para
                        todos los candidatos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Formation (Timeline Style) -->
    <section id="formacion" class="py-24 bg-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-bold text-slate-900">Laboratorio de Líderes</h2>
                    <p class="mt-4 text-slate-600">Proceso de certificación obligatorio. Aquí se estudia para servir.
                    </p>
                </div>
                <a href="/admin/register"
                    class="mt-4 md:mt-0 px-6 py-3 bg-slate-900 text-white font-medium rounded hover:bg-slate-700 transition">
                    Ver Curso
                </a>
            </div>

            <div class="space-y-4">
                <div class="flex items-center bg-white p-6 rounded border border-slate-200">
                    <div
                        class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-600 mr-6">
                        1</div>
                    <div>
                        <h3 class="font-bold text-slate-900">Integridad y Ética</h3>
                        <p class="text-sm text-slate-600">El filtro de confianza fundamental.</p>
                    </div>
                </div>
                <div class="flex items-center bg-white p-6 rounded border border-slate-200">
                    <div
                        class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-600 mr-6">
                        2</div>
                    <div>
                        <h3 class="font-bold text-slate-900">Gerencia de Proyectos</h3>
                        <p class="text-sm text-slate-600">Modelos de gestión aplicados al barrio.</p>
                    </div>
                </div>
                <div class="flex items-center bg-white p-6 rounded border border-slate-200">
                    <div
                        class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-600 mr-6">
                        3</div>
                    <div>
                        <h3 class="font-bold text-slate-900">Derecho Territorial</h3>
                        <p class="text-sm text-slate-600">Defensa de la Ley 70 y derechos humanos.</p>
                    </div>
                </div>
                <div class="flex items-center bg-white p-6 rounded border border-slate-200">
                    <div
                        class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-600 mr-6">
                        4</div>
                    <div>
                        <h3 class="font-bold text-slate-900">Gobierno Digital</h3>
                        <p class="text-sm text-slate-600">Transparencia y comunicación efectiva.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Proposals (List) -->
    <section id="propuestas" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-slate-900 mb-12 text-center">Nodos de Excelencia</h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                <div>
                    <h4 class="font-bold text-xl mb-4 border-b border-amber-500 pb-2 inline-block">Desarrollo Humano
                    </h4>
                    <ul class="space-y-2 text-slate-600">
                        <li>• Educación técnica dual</li>
                        <li>• Telemedicina rural</li>
                        <li>• Inclusión Ley 70</li>
                        <li>• Deporte y salud</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-xl mb-4 border-b border-blue-500 pb-2 inline-block">Infraestructura</h4>
                    <ul class="space-y-2 text-slate-600">
                        <li>• Vías terciarias productivas</li>
                        <li>• Vivienda bioclimática</li>
                        <li>• Conectividad total (TIC)</li>
                        <li>• Energías limpias</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-xl mb-4 border-b border-green-500 pb-2 inline-block">Economía</h4>
                    <ul class="space-y-2 text-slate-600">
                        <li>• Bio-Tecnología de Juntanza</li>
                        <li>• Turismo ecológico</li>
                        <li>• Primer empleo garantizado</li>
                        <li>• Auditoría ciudadana</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Transparency (High Contrast) -->
    <section id="transparencia" class="py-24 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-3xl mx-auto">
                <span class="text-amber-500 font-bold uppercase tracking-wider text-sm mb-2 block">Veeduría
                    Digital</span>
                <h2 class="text-4xl font-bold mb-6">Tu celular es tu curul.</h2>
                <p class="text-xl text-slate-300 mb-10 leading-relaxed">
                    Hemos creado un sistema donde cada peso público es visible. Sin claves secretas, sin burocracia. Tú
                    eres el auditor.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left mb-10">
                    <div class="bg-slate-800 p-6 rounded border border-slate-700">
                        <strong class="block text-white mb-2">Presupuesto</strong>
                        <p class="text-slate-400 text-sm">Visualización en tiempo real de gastos.</p>
                    </div>
                    <div class="bg-slate-800 p-6 rounded border border-slate-700">
                        <strong class="block text-white mb-2">Proyectos</strong>
                        <p class="text-slate-400 text-sm">Estado de avance de obras con fotos.</p>
                    </div>
                    <div class="bg-slate-800 p-6 rounded border border-slate-700">
                        <strong class="block text-white mb-2">Denuncias</strong>
                        <p class="text-slate-400 text-sm">Canal directo y anónimo.</p>
                    </div>
                </div>
                <a href="/admin"
                    class="inline-block px-8 py-4 bg-amber-600 text-white font-bold rounded hover:bg-amber-700 transition">
                    Acceder al Portal de Transparencia
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Simple -->
    <footer id="contacto" class="bg-slate-50 border-t border-slate-200 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12">
                <div>
                    <strong class="block text-lg text-slate-900 mb-4">DEMOSOL</strong>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Cali, Valle del Cauca: C.C. El Limonar Cl. 13 # 68-64<br>
                        Medellín, Antioquia: Edificio Nuevo Centro La Alpujarra
                    </p>
                </div>
                <div>
                    <strong class="block text-lg text-slate-900 mb-4">Contacto</strong>
                    <p class="text-slate-500 text-sm">
                        demosol2050@gmail.com<br>
                        Línea Nacional de Soporte
                    </p>
                </div>
                <div>
                    <strong class="block text-lg text-slate-900 mb-4">Legal</strong>
                    <ul class="text-slate-500 text-sm space-y-2">
                        <li><a href="#" class="hover:underline">Política de Privacidad</a></li>
                        <li><a href="#" class="hover:underline">Términos de Uso</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-slate-200 text-center text-slate-400 text-sm">
                &copy; {{ date('Y') }} DEMOSOL.
            </div>
        </div>
    </footer>

</body>

</html>