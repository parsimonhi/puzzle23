// when a resource is modified, then change the version to force a cache update
// execute Date.now() in the console to generate the number
const VERSION="v1763370110192";
console.log(VERSION);
// the name of the cache
const CACHE_NAME=`puzzle23-${VERSION}`;

// the static resources that the app needs to function
const APP_STATIC_RESOURCES=[
	"./",
	"manifest.json",
	"images.json",
	"_i18n/puzzle-i18n-fr.json",
	"_icon/home.svg",
	"_icon/open.svg",
	"_icon/preferences.svg",
	"_icon/help.svg",
	"_icon/puzzle.svg",
	"_icon/puzzle-not-found.svg",
	"_icon/puzzle512.png",
	"_icon/restart.svg",
	"_icon/reframe.svg",
	"_icon/visual-impairments.svg",
	"_icon/wait.svg",
	"_img/_animal/Mandarin-duck-Aix-galericulata.webp",
	"_img/_animal/Mandarin-duck-Aix-galericulata-small.webp",
	"_img/_animal/Rhinoceros.webp",
	"_img/_animal/Rhinoceros-small.webp",
	"_img/_animal/Rosy-faced-lovebird-Agapornis-roseicollis.webp",
	"_img/_animal/Rosy-faced-lovebird-Agapornis-roseicollis-small.webp",
	"_img/_animal/Snow_leopard_portrait-2010-07-09.webp",
	"_img/_animal/Snow_leopard_portrait-2010-07-09-small.webp",
	"_img/_animal/Spurge-caterpillar.webp",
	"_img/_animal/Spurge-caterpillar-small.webp",
	"_img/_animal/Trilobite-zlichovaspis-rugosa.webp",
	"_img/_animal/Trilobite-zlichovaspis-rugosa-small.webp",
	"_img/_animal/Tyto-alba.webp",
	"_img/_animal/Tyto-alba-small.webp",
	"_img/_animal/Unenlagia_comahuensis_reconstructed_skeleton.webp",
	"_img/_animal/Unenlagia_comahuensis_reconstructed_skeleton-small.webp",
	"_img/_art/A-Friend-in-Need-C-M-Coolidge.webp",
	"_img/_art/A-Friend-in-Need-C-M-Coolidge-small.webp",
	"_img/_art/Banksy-judge-beating-a-protester.webp",
	"_img/_art/Banksy-judge-beating-a-protester-small.webp",
	"_img/_art/Hieroglyphs_from_the_tomb_of_Seti_I.webp",
	"_img/_art/Hieroglyphs_from_the_tomb_of_Seti_I-small.webp",
	"_img/_art/Panneau-des-chevaux-Cosquer-Mediterranee.webp",
	"_img/_art/Panneau-des-chevaux-Cosquer-Mediterranee-small.webp",
	"_img/_art/Pierre-Auguste-Renoir-Luncheon-of-the-Boating-Party.webp",
	"_img/_art/Pierre-Auguste-Renoir-Luncheon-of-the-Boating-Party-small.webp",
	"_img/_building/Colosseum-Rome.webp",
	"_img/_building/Colosseum-Rome-small.webp",
	"_img/_building/Nuuk-Greenland.webp",
	"_img/_building/Nuuk-Greenland-small.webp",
	"_img/_clothing/Venice-Carnival-Masked-Lovers.webp",
	"_img/_clothing/Venice-Carnival-Masked-Lovers-small.webp",
	"_img/_object/Vesta-sewing-machine.webp",
	"_img/_object/Vesta-sewing-machine-small.webp",
	"_img/_landscape/Horseshoe-Bend.webp",
	"_img/_landscape/Horseshoe-Bend-small.webp",
	"_img/_plant/Adansonia_grandidieri04.webp",
	"_img/_plant/Adansonia_grandidieri04-small.webp",
	"_img/_plant/Cross-section-ginkgo-biloba.webp",
	"_img/_plant/Cross-section-ginkgo-biloba-small.webp",
	"_img/_plant/Zygopetalum.webp",
	"_img/_plant/Zygopetalum-small.webp",
	"_img/_transport/International-Space-Station.webp",
	"_img/_transport/International-Space-Station-small.webp",
	"_img/_transport/Avion_III_20050711.webp",
	"_img/_transport/Avion_III_20050711-small.webp"
];

// on install, cache the static resources
self.addEventListener("install",(event)=>
{
	event.waitUntil(
		(async ()=>
		{
			const cache=await caches.open(CACHE_NAME);
			cache.addAll(APP_STATIC_RESOURCES);
		})()
	);
});

// on activate, delete old caches
self.addEventListener("activate",(event)=>
{
	event.waitUntil(
		(async ()=>
		{
			const names=await caches.keys();
			await Promise.all(
				names.map((name)=>
				{
					// if the worker version changed, delete the old cache
					if(name!==CACHE_NAME) return caches.delete(name);
					return undefined;
				})
			);
			await clients.claim();
		})()
	);
});

const putInCache=async (request,response)=>
{
	const cache=await caches.open(CACHE_NAME);
	await cache.put(request,response);
};

const cacheFirst=async ({request,event})=>
{
	// first try to get the resource from the cache
	const responseFromCache=await caches.match(request);
	if (responseFromCache) return responseFromCache;
	// next try to get the resource from the network
	try
	{
		// second try to get the ressource from the net
		// todo: manage "opaque response" if any
		const responseFromNetwork=await fetch(request);
		// cache the ressource
		// response may be used only once, then clone it
		// put one copy in cache and serve the second one
		event.waitUntil(putInCache(request,responseFromNetwork.clone()));
		return responseFromNetwork;
	}
	catch(error)
	{
		const fallbackResponse=await caches.match("_icon/puzzle-not-found.svg");
		if (fallbackResponse) return fallbackResponse;
		// nothing more can be done
		// but one must always return a Response object
		console.log(error);
		console.log(request);
		return new Response("Plouf",
		{
			status: 408,
			headers: {"Content-Type":"text/plain"}
		});
	}
};

self.addEventListener("fetch",(event)=>
{
	event.respondWith(
		cacheFirst(
		{
			request:event.request,
			event
		})
	);
});