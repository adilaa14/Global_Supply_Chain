<?php
$images = [
    '1586528116311-ad8dd3c8310d', '1494412519320-ce1b9426f03d', '1578575437130-527eed3abbec',
    '1504917595217-d4dc5ebe6122', '1601584115197-04ecc0da31d7', '1518186285589-2f7649de83e0',
    '1521587760476-6c12a4b040da', '1553413077-190dd305871c', '1621293954908-907159247fc8',
    '1566367576585-0512ff3dbdc9', '1580674292150-1df087198eec', '1615840287214-722a9f5d3ab4',
    '1611974789855-9c2a0a7236a3', '1460925895917-afdab827c52f', '1590283603385-17ffb3a7f29f',
    '1526304640581-d334cdbbf45e', '1507679622140-5e51d6db0f2a', '1454165804606-c3d57bc86b40',
    '1551288049-bebda4e38f71', '1486406146926-c627a92ad1ab', '1535320903-0ec80f553ee7',
    '1523995462485-3d171b5c4fac', '1534438327276-14e5300c3a48', '1529078155058-05a490074d2d',
    '1503698059885-38435d8af876', '1584483760906-c78c15157b1d', '1526270457630-3165b45265e3'
];

foreach ($images as $img) {
    $ch = curl_init("https://images.unsplash.com/photo-{$img}?w=800&q=80");
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200) {
        echo "BROKEN: $img ($code)\n";
    }
}
echo "Done testing.\n";
