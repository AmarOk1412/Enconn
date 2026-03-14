---
title: Computing distances between activities
subtitle: to create a travel guide
date: 2026-03-13
tags: ["programming", "geocoding", "dev", "quebec-guide"]
comments: true
---

I didn't do much this work, busy during the day, doing nothing at night (yeah, my bad, One Piece season 2 is there).
I'm mostly preparing different stuff that I'll not describe for now, and this week end, I've got 2 full days in a forge doing stuff.

So this week, I'll talk about a project I do in my spare time. I want to write a guide for travelers to visit Quebec.
The goal is pretty simple, users answer to some questions and they get a custom planning for their vacations.
Behind this simple idea, there is still a bunch of subjects to think of.

And for this week, here is a small subject. I need to search activities in a sector, or check similar activities nearby. How can I do this?

# Postal codes

![Postal Codes](/img/dev/h3-geocoding/postal-code.jpg)

In Québec/Canada, there is a certain sort of logic with postal codes, but. Nah, don't. In the screenshot, it's a simple case just for your information.

# Latitude/Longitude

I guess the first reflex for a developer would be to go for latitude/longitude. It's pretty simple, because it encodes every point in the world with 2 numbers with infinite precision.
The standard is around 4/5 digits, providing a precision of 10 meters. 2 are around 1km. This seems fine.

So, now the step is to transform an address to a coordinate (lat,lon). This can easily be done via a really helpful project (which is free as freedom): [OpenStreetMap](https://www.openstreetmap.org/)

OpenStreetMap has a geocoding software with an API: [nominatim](https://nominatim.openstreetmap.org/search) that can provide a simple JSON to parse.

For *Hotel de ville, Montréal* I can get a JSON, where I get the first candidate and retrieve the linked latitude/longitude: (**45.5088383,-73.554142**). Then for *La Tour à Bières, Chicoutimi*, I have (**48.4290195,-71.0522944**).

To correctly use Nominatim, you can search the address first on OpenStreetMap. There are generally some differences with Google Maps (*A.* vs *Ave* vs *Avenue* or *Rd* vs *Road*, etc). So, avoid maps!
To compute the distance in km between two destinations, we can use the Haversine formula: $$d = 2R \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos\phi_1 \cos\phi_2 \sin^2\left(\frac{\Delta\lambda}{2}\right)}\right)$$

where $$\Delta\phi$$ is the delta of latitudes in radians, $$\Delta\lambda$$ the delta of longitudes in radians, $R$ is the earth radius (~6371 km).
In our case: this gives 376.1km (remove some digits and you will be around ~330km).

To be honest, this is fine, but slow and need to compute things. We can do better than that if we think about datas.

# Hash encoding

Remember my previous articles about [DHTs](http://localhost:1313/blog/p2p-internals-dht/). Computing distances between hashes is really trivial. It's just a *XOR* operation. So, it would be perfect to transform a coordinate to a hash. The nearest the hash, the nearest the activity.

And, the good news, is I'm not the first to have thought about this. This is a common problem for Uber (and in aerospace) and they came with a solution for this. Introducing [h3](https://h3geo.org)

> H3 is a hierarchical geospatial index. H3 indexes refer to cells by the spatial hierarchy. Every hexagonal cell, up to the maximum resolution supported by H3, has seven child cells below it in this hierarchy. This subdivision is referred to as aperture 7.

![H3](/img/dev/h3-geocoding/parent-child.png)

The idea is simple. The world is divided in hexagons and 12 pentagons of a certain size. Then, every hexagon is divided in 7 sub-sections until a precision of 1m (so, 569,707,381,193,162 which is under a 64-bit integer). Here is the resolution:

![H3 resolution](/img/dev/h3-geocoding/resolution.png)
Source: https://h3geo.org/docs/core-library/restable#average-area-in-km2

So to find the activities nearby, you can just consider a resolution of 5 or 6 and neighbors to get ~10 or 50km precision, which is good enough depending on the transportation mode the traveler uses. Here is an example with 842ba13ffffffff and 842ba1bffffffff:

![H3 example](/img/dev/h3-geocoding/h3-example.png)


Finally, to use it, I found a crate in Rust: https://docs.rs/h3o/latest/h3o/ taking a LatLng in input, giving a h3 code in output.

# Final note

The only thing is that it's not a travel distance, but the straight distance between two points, but it's useful to get similar activities nearby, not traveling time (yet).