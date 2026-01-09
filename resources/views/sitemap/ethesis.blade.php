<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($etheses as $thesis)
    <url>
        <loc>{{ route('opac.ethesis.show', $thesis->id) }}</loc>
        <lastmod>{{ $thesis->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
