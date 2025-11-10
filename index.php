<?php
declare(strict_types=1);
include "Class/Band.php";
include "Class/Song.php";
include "Class/Link.php";
include "Class/Album.php";
include "Class/Member.php";

/**
 * Bands – OOP Rendering (Student Task Skeleton)
 * ---------------------------------------------------------
 * Read bands_full.json → build objects (Link, Member, Song, Album, Band) → print with getters.
 * DO NOT access public properties directly in the view.
 * Keep all JSON → Object mapping in THIS file for today’s task (no separate loader).
 */


/****** build objects (Link, Member, Song, Album, Band) *********************************************** */
$bands = [];

foreach ($data['bands'] as $bandData) {
    // Links
    $links = null;
    if (isset($bandData['links']) && is_array($bandData['links'])) {
        $links = new Link(
            $bandData['links']['website'] ?? null,
            $bandData['links']['wikipedia'] ?? null,
            $bandData['links']['spotify'] ?? null,
            $bandData['links']['youtube'] ?? null
        );
    }

    // Members
    $members = [];
    foreach ($bandData['members'] ?? [] as $m) {
        $members[] = new Member(
            $m['name'] ?? 'Unknown',
            $m['role'] ?? 'Unknown',
            (int)($m['joined'] ?? 0)
        );
    }

    // Albums + Songs
    $albums = [];
    foreach ($bandData['albums'] ?? [] as $a) {
        $songs = [];
        foreach ($a['songs'] ?? [] as $s) {
            $songs[] = new Song($s['title'] ?? 'Untitled', $s['length'] ?? '0:00');
        }

        $albums[] = new Album(
            $a['title'] ?? 'Unknown Album',
            (int)($a['release_year'] ?? 0),
            $a['genre'] ?? 'Unknown',
            $songs
        );
    }

    // Band
    $bands[] = new Band(
        $bandData['name'] ?? 'Unknown Band',
        (int)($bandData['founded'] ?? 0),
        $bandData['origin'] ?? 'Unknown',
        $bandData['genres'] ?? [],
        $members,
        $albums,
        $links
    );
}

/***************************************************** */
/**
 * EXAMPLES OF OBJECTS
 */

// 1. Create Song objects
$songs = [
    new Song("Battery", "05:12"),
    new Song("Master of Puppets", "08:36"),
    new Song("Welcome Home (Sanitarium)", "06:27"),
];

// 2. Create an Album object and attach the songs
$album = new Album(
    title: "Master of Puppets",
    releaseYear: 1986,
    genre: "Thrash Metal",
    songs: $songs
);

// 3. Create Member objects
$members = [
    new Member("James Hetfield", "Vocals, Rhythm Guitar", 1981),
    new Member("Lars Ulrich", "Drums", 1981),
    new Member("Kirk Hammett", "Lead Guitar", 1983),
    new Member("Robert Trujillo", "Bass", 2003),
];

// 4. Create Link object
$link = new Link(
    website: "https://www.metallica.com",
    wikipedia: "https://en.wikipedia.org/wiki/Metallica",
    spotify: "https://open.spotify.com/artist/2ye2Wgw4gimLv2eAKyk1NB",
    youtube: "https://www.youtube.com/@Metallica"
);

// 5. Create the Band object
$band = new Band(
    name: "Metallica",
    founded: 1981,
    origin: "Los Angeles, California, USA",
    genres: ["Heavy Metal", "Thrash Metal"],
    members: $members,
    albums: [$album],
    links: $link
);

// === OUTPUT EXAMPLES ===

// Print the band’s basic info
echo "Band: " . $band->getName() . PHP_EOL;
echo "Founded: " . $band->getFounded() . PHP_EOL;
echo "Origin: " . $band->getOrigin() . PHP_EOL;

// Print members
echo PHP_EOL . "Members:" . PHP_EOL;
foreach ($band->getMembers() as $m) {
    echo "- {$m->getName()} ({$m->getRole()}, joined {$m->getJoined()})" . PHP_EOL;
}

// Print albums and songs
echo PHP_EOL . "Albums:" . PHP_EOL;
foreach ($band->getAlbums() as $a) {
    echo "{$a->getTitle()} ({$a->getReleaseYear()}) - {$a->getGenre()}" . PHP_EOL;
    foreach ($a->getSongs() as $s) {
        echo "   → {$s->getTitle()} ({$s->getLength()})" . PHP_EOL;
    }
}

// Print links
echo PHP_EOL . "Official website: " . $band->getLinks()?->getWebsite() . PHP_EOL;


// ---------------------------------------------------------
// TODO 1: READ & DECODE THE JSON FILE
// ---------------------------------------------------------
/**
 
 * GOAL:
 * - Read `bands_full.json` from the project root.
 * - Decode it to an associative array.
 * - Validate the shape and handle errors gracefully.
 *
 * HINTS:
 * - Verify the file exists before reading.
 * - Use a readable error message for each failure mode:
 *   - file not found
 *   - read failure
 *   - invalid JSON
 *   - missing "bands" key or wrong type
 *
 * ACCEPTANCE:
 * - You end up with a variable like $data where $data['bands'] is an array of band entries.
 *
 * WRITE YOUR CODE BELOW:
 */
// $jsonPath = __DIR__ . '/bands_full.json';
// $json     = ...
// $data     = ...
// Validate structure here...
$jsonPath = __DIR__ . '/bands_full.json';
if (!file_exists($jsonPath)) {
    die("Error: JSON file not found at $jsonPath");
}

$json = file_get_contents($jsonPath);
if ($json === false) {
    die("Error: Could not read the JSON file.");
}

$data = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid JSON format — " . json_last_error_msg());
}

if (!isset($data['bands']) || !is_array($data['bands'])) {
    die("Error: JSON must contain a 'bands' array.");
}


// ---------------------------------------------------------
// TODO 2: BUILD OBJECTS FROM JSON
// ---------------------------------------------------------
/**
 * GOAL:
 * - Iterate through $data['bands'] and, for each band item:
 *   1) Build a Link object (nullable) from keys: website, wikipedia, spotify, youtube.
 *   2) Build an array of Member objects (name, role, joined).
 *   3) For each album:
 *      - Build an array of Song objects (title, length).
 *      - Build an Album object (title, release_year, genre, songs[]).
 *   4) Finally, build a Band object:
 *      - name, founded, origin, genres[], members[], albums[], links?
 *   5) Push each Band into a $bands array.
 *
 * RULES:
 * - Initialize $members, $albums, $songs, and $links INSIDE the loop for each band.
 * - Use constructors and type hints as defined in your classes.
 * - If a key is missing, decide a safe default (empty array, null, or sensible fallback).
 * - Do NOT echo/print here — keep this block for object construction only.
 *
 * ACCEPTANCE:
 * - $bands is an array that contains one Band object per band in JSON.
 *
 * WRITE YOUR CODE BELOW:
 */

$bands = [];
// foreach ($data['bands'] as $bandData) {
//     // 2.1 Links (nullable)
//     //    - If "links" exists and is an array, create a Link object.
//     //    - Otherwise, keep $links = null.
//
//     // 2.2 Members
//     //    - Start with an empty array: $members = [];
//     //    - Loop "members" and create Member objects from (name, role, joined).
//
//     // 2.3 Albums + Songs
//     //    - Start with an empty array: $albums = [];
//     //    - For each album:
//     //        a) Build $songs = [];
//     //        b) Loop songs and create Song objects (title, length).
//     //        c) Create Album object with title, release_year, genre, and $songs.
//
//     // 2.4 Band
//     //    - Create Band object with name, founded, origin, genres, $members, $albums, $links.
//     //    - Append to $bands.
// }

$bands = [];

foreach ($data['bands'] as $bandData) {

    // 2.1 Links (nullable)
    $links = null;
    if (isset($bandData['links']) && is_array($bandData['links'])) {
        $links = new Link(
            website: $bandData['links']['website'] ?? null,
            wikipedia: $bandData['links']['wikipedia'] ?? null,
            spotify: $bandData['links']['spotify'] ?? null,
            youtube: $bandData['links']['youtube'] ?? null
        );
    }

    // 2.2 Members
    $members = [];
    if (isset($bandData['members']) && is_array($bandData['members'])) {
        foreach ($bandData['members'] as $mem) {
            $members[] = new Member(
                $mem['name'] ?? 'Unknown',
                $mem['role'] ?? 'Unknown',
                (int)($mem['joined'] ?? 0)
            );
        }
    }

    // 2.3 Albums + Songs
    $albums = [];
    if (isset($bandData['albums']) && is_array($bandData['albums'])) {
        foreach ($bandData['albums'] as $alb) {
            $songs = [];
            if (isset($alb['songs']) && is_array($alb['songs'])) {
                foreach ($alb['songs'] as $s) {
                    $songs[] = new Song(
                        $s['title'] ?? 'Untitled',
                        $s['length'] ?? '00:00'
                    );
                }
            }

            $albums[] = new Album(
                title: $alb['title'] ?? 'Unknown Album',
                releaseYear: (int)($alb['release_year'] ?? 0),
                genre: $alb['genre'] ?? 'Unknown',
                songs: $songs
            );
        }
    }

    // 2.4 Band
    $bands[] = new Band(
        name: $bandData['name'] ?? 'Unnamed Band',
        founded: (int)($bandData['founded'] ?? 0),
        origin: $bandData['origin'] ?? 'Unknown',
        genres: $bandData['genres'] ?? [],
        members: $members,
        albums: $albums,
        links: $links
    );
}



// ---------------------------------------------------------
// TODO 3: RENDER – PRINT USING GETTERS ONLY
// ---------------------------------------------------------
/**
 * GOAL:
 * - Output a clean HTML view of all bands.
 * - Use ONLY getters on your objects (no direct property access).
 * - Escape user-visible text (e.g., with htmlspecialchars) to avoid XSS issues.
 *
 * MINIMUM CONTENT PER BAND:
 * - Name, founded year, origin
 * - Genres (comma-separated)
 * - Member list: "Name — Role (JoinedYear)"
 * - Albums:
 *     - For each album: "Title (Year) — Genre"
 *     - Songs under each album: "Title (Length)"
 * - Links (if present): Website / Wikipedia / Spotify / YouTube
 *
 * UX HINTS:
 * - Use headings and lists for readability.
 * - If a specific field is missing, either skip it or show a friendly fallback.
 *
 * ACCEPTANCE:
 * - All output comes from getters.
 * - The page loads without notices/warnings/fatal errors.
 * - The structure matches the JSON content.
 */


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bands (OOP)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root { color-scheme: light dark; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 2rem; line-height: 1.5; }
        h1 { margin-bottom: 1rem; }
        .band { padding: 1rem 0; border-bottom: 1px solid #ccc; }
        .meta { color: #555; }
        ul { margin: .25rem 0 1rem; }
        a { text-decoration: none; }
    </style>
</head>
<body>
<h1>Band Catalog</h1>

<?php
/**
 * TODO 4: LOOP & PRINT
 * - Loop through $bands
 * - Print required fields using getters only
 * - Escape text output (e.g., htmlspecialchars($band->getName()))
 * - Check if links exist before printing them
 *
 * OPTIONAL:
 * - Limit long lists (e.g., show first 2 songs) if the output becomes too large.
 * - Add simple counts (albums per band, songs per album).
 */

// Example structure (NOT real code – write your own):
// foreach ($bands as $band) {
//   echo "<section class='band'>";
//   // Band basic info (name, founded, origin, genres)
//   // Members list
//   // Albums with songs
//   // Links (conditionals for each)
//   echo "</section>";
// }

foreach ($bands as $band): ?>
    <section class="band">
        <h2><?= htmlspecialchars($band->getName()) ?></h2>
        <div class="meta">
            Founded: <?= htmlspecialchars((string)$band->getFounded()) ?> —
            Origin: <?= htmlspecialchars($band->getOrigin()) ?><br>
            Genres: <?= htmlspecialchars(implode(', ', $band->getGenres())) ?>
        </div>

        <h3>Members</h3>
        <ul>
            <?php foreach ($band->getMembers() as $m): ?>
                <li><?= htmlspecialchars($m->getName()) ?> — <?= htmlspecialchars($m->getRole()) ?> (joined <?= htmlspecialchars((string)$m->getJoined()) ?>)</li>
            <?php endforeach; ?>
        </ul>

        <h3>Albums</h3>
        <?php foreach ($band->getAlbums() as $a): ?>
            <strong><?= htmlspecialchars($a->getTitle()) ?></strong>
            (<?= htmlspecialchars((string)$a->getReleaseYear()) ?>) — <?= htmlspecialchars($a->getGenre()) ?><br>
            <ul>
                <?php foreach ($a->getSongs() as $s): ?>
                    <li><?= htmlspecialchars($s->getTitle()) ?> (<?= htmlspecialchars($s->getLength()) ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>

        <?php if ($band->getLinks()): ?>
            <h3>Links</h3>
            <ul>
                <?php if ($band->getLinks()->getWebsite()): ?>
                    <li><a href="<?= htmlspecialchars($band->getLinks()->getWebsite()) ?>" target="_blank">Website</a></li>
                <?php endif; ?>
                <?php if ($band->getLinks()->getWikipedia()): ?>
                    <li><a href="<?= htmlspecialchars($band->getLinks()->getWikipedia()) ?>" target="_blank">Wikipedia</a></li>
                <?php endif; ?>
                <?php if ($band->getLinks()->getSpotify()): ?>
                    <li><a href="<?= htmlspecialchars($band->getLinks()->getSpotify()) ?>" target="_blank">Spotify</a></li>
                <?php endif; ?>
                <?php if ($band->getLinks()->getYoutube()): ?>
                    <li><a href="<?= htmlspecialchars($band->getLinks()->getYoutube()) ?>" target="_blank">YouTube</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endforeach; ?>


<?php
foreach ($bands as $band) {
    echo "<section class='band'>";
    echo "<h2>" . htmlspecialchars($band->getName()) . "</h2>";
    echo "<div class='meta'>Founded: " . htmlspecialchars((string)$band->getFounded()) . " | Origin: " . htmlspecialchars($band->getOrigin()) . "</div>";
    echo "<div class='meta'>Genres: " . htmlspecialchars(implode(", ", $band->getGenres())) . "</div>";

    // Members
    echo "<h3>Members:</h3><ul>";
    foreach ($band->getMembers() as $member) {
        echo "<li>" . htmlspecialchars($member->getName()) . " — " . htmlspecialchars($member->getRole()) . " (Joined " . htmlspecialchars((string)$member->getJoined()) . ")</li>";
    }
    echo "</ul>";

    // Albums and Songs
    echo "<h3>Albums:</h3>";
    foreach ($band->getAlbums() as $album) {
        echo "<h4>" . htmlspecialchars($album->getTitle()) . " (" . htmlspecialchars((string)$album->getReleaseYear()) . ") — " . htmlspecialchars($album->getGenre()) . "</h4><ul>";
        foreach ($album->getSongs() as $song) {
            echo "<li>" . htmlspecialchars($song->getTitle()) . " (" . htmlspecialchars($song->getLength()) . ")</li>";
        }
        echo "</ul>";
    }

    // Links
    $links = $band->getLinks();
    if ($links !== null) {
        echo "<h3>Links:</h3><ul>";
        if ($links->getWebsite() !== null) {
            echo "<li><a href='" . htmlspecialchars($links->getWebsite()) . "' target='_blank'>Website</a></li>";
        }
        if ($links->getWikipedia() !== null) {
            echo "<li><a href='" . htmlspecialchars($links->getWikipedia()) . "' target='_blank'>Wikipedia</a></li>";
        }
        if ($links->getSpotify() !== null) {
            echo "<li><a href='" . htmlspecialchars($links->getSpotify()) . "' target='_blank'>Spotify</a></li>";
        }
        if ($links->getYouTube() !== null) {
            echo "<li><a href='" . htmlspecialchars($links->getYou
?>

</body>
</html>
