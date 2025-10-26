<?php
use Ptilz\Color;
use Ptilz\Math;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase {

    static $huslTests;

    public static function setUpBeforeClass(): void {
        self::$huslTests = \Ptilz\Json::loadFile(__DIR__ . '/husl-rev4.json');
    }

    function testRgbToHsl() {
        $this->assertSame([0., 0., 0.], Color::rgbToHsl(0, 0, 0));
        $this->assertSame([0., 0., 1.], Color::rgbToHsl(255, 255, 255));
        $this->assertSame([0., 0., .5], Color::rgbToHsl(127.5, 127.5, 127.5));
        $this->assertEqualsWithDelta([218/360, 1, .98], Color::rgbToHsl(244, 248, 255), 0.01, 'aliceblue');
        $this->assertEqualsWithDelta([37/360, 1., .5625], Color::rgbToHsl(255, 171, 32), 0.01, 'local_closure_goog_color_color_test');
        $this->assertSame([0/360., 1., .5], Color::rgbToHsl(255, 0, 0));
        $this->assertSame([60/360., 1., .5], Color::rgbToHsl(255, 255, 0));
        $this->assertSame([120/360., 1., .5], Color::rgbToHsl(0, 255, 0));
        $this->assertSame([180/360., 1., .5], Color::rgbToHsl(0, 255, 255));
        $this->assertSame([240/360., 1., .5], Color::rgbToHsl(0, 0, 255));
        $this->assertSame([300/360., 1., .5], Color::rgbToHsl(255, 0, 255));

        $this->assertEqualsWithDelta([204/360, 30/100, 51/100], Color::rgbToHsl(93, 138, 168), 0.01, "Air Force Blue");
        $this->assertEqualsWithDelta([208/360, 100/100, 97/100], Color::rgbToHsl(240, 248, 255), 0.01, "Alice Blue");
        $this->assertEqualsWithDelta([355/360, 77/100, 52/100], Color::rgbToHsl(227, 38, 54), 0.01, "Alizarin");
        $this->assertEqualsWithDelta([348/360, 78/100, 53/100], Color::rgbToHsl(229, 43, 80), 0.01, "Amaranth");
        $this->assertEqualsWithDelta([45/360, 100/100, 50/100], Color::rgbToHsl(255, 191, 0), 0.01, "Amber");
        $this->assertEqualsWithDelta([74/360, 55/100, 50/100], Color::rgbToHsl(164, 198, 57), 0.01, "Android Green");
        $this->assertEqualsWithDelta([74/360, 100/100, 36/100], Color::rgbToHsl(141, 182, 0), 0.01, "Apple Green");
        $this->assertEqualsWithDelta([24/360, 90/100, 84/100], Color::rgbToHsl(251, 206, 177), 0.01, "Apricot");
        $this->assertEqualsWithDelta([160/360, 100/100, 75/100], Color::rgbToHsl(127, 255, 212), 0.01, "Aquamarine");
        $this->assertEqualsWithDelta([69/360, 44/100, 23/100], Color::rgbToHsl(75, 83, 32), 0.01, "Army Green");
        $this->assertEqualsWithDelta([206/360, 12/100, 26/100], Color::rgbToHsl(59, 68, 75), 0.01, "Arsenic");
        $this->assertEqualsWithDelta([51/360, 74/100, 67/100], Color::rgbToHsl(233, 214, 107), 0.01, "Arylide Yellow");
        $this->assertEqualsWithDelta([135/360, 8/100, 72/100], Color::rgbToHsl(178, 190, 181), 0.01, "Ash Grey");
        $this->assertEqualsWithDelta([93/360, 27/100, 54/100], Color::rgbToHsl(135, 169, 107), 0.01, "Asparagus");
        $this->assertEqualsWithDelta([20/360, 100/100, 70/100], Color::rgbToHsl(255, 153, 102), 0.01, "Atomic Tangerine");
        $this->assertEqualsWithDelta([20/360, 61/100, 26/100], Color::rgbToHsl(109, 53, 26), 0.01, "Auburn");
        $this->assertEqualsWithDelta([210/360, 100/100, 50/100], Color::rgbToHsl(0, 127, 255), 0.01, "Azure");
        $this->assertEqualsWithDelta([199/360, 77/100, 74/100], Color::rgbToHsl(137, 207, 240), 0.01, "Baby Blue");
        $this->assertEqualsWithDelta([209/360, 74/100, 79/100], Color::rgbToHsl(161, 202, 241), 0.01, "Baby Blue Eyes");
        $this->assertEqualsWithDelta([0/360, 69/100, 86/100], Color::rgbToHsl(244, 194, 194), 0.01, "Baby Pink");
        $this->assertEqualsWithDelta([47/360, 100/100, 58/100], Color::rgbToHsl(255, 209, 42), 0.01, "Banana Yellow");
        $this->assertEqualsWithDelta([60/360, 1/100, 51/100], Color::rgbToHsl(132, 132, 130), 0.01, "Battleship Grey");
        $this->assertEqualsWithDelta([353/360, 14/100, 53/100], Color::rgbToHsl(152, 119, 123), 0.01, "Bazaar");
        $this->assertEqualsWithDelta([60/360, 56/100, 91/100], Color::rgbToHsl(245, 245, 220), 0.01, "Beige");
        $this->assertEqualsWithDelta([24/360, 33/100, 18/100], Color::rgbToHsl(61, 43, 31), 0.01, "Bistre");
        $this->assertEqualsWithDelta([0/360, 0/100, 0/100], Color::rgbToHsl(0, 0, 0), 0.01, "Black");
        $this->assertEqualsWithDelta([210/360, 79/100, 55/100], Color::rgbToHsl(49, 140, 231), 0.01, "Bleu De France");
        $this->assertEqualsWithDelta([50/360, 86/100, 86/100], Color::rgbToHsl(250, 240, 190), 0.01, "Blond");
        $this->assertEqualsWithDelta([240/360, 100/100, 50/100], Color::rgbToHsl(0, 0, 255), 0.01, "Blue");
        $this->assertEqualsWithDelta([342/360, 66/100, 62/100], Color::rgbToHsl(222, 93, 131), 0.01, "Blush");
        $this->assertEqualsWithDelta([9/360, 34/100, 35/100], Color::rgbToHsl(121, 68, 59), 0.01, "Bole");
        $this->assertEqualsWithDelta([0/360, 100/100, 40/100], Color::rgbToHsl(204, 0, 0), 0.01, "Boston University Red");
        $this->assertEqualsWithDelta([52/360, 47/100, 48/100], Color::rgbToHsl(181, 166, 66), 0.01, "Brass");
        $this->assertEqualsWithDelta([96/360, 100/100, 50/100], Color::rgbToHsl(102, 255, 0), 0.01, "Bright Green");
        $this->assertEqualsWithDelta([272/360, 60/100, 74/100], Color::rgbToHsl(191, 148, 228), 0.01, "Bright Lavender");
        $this->assertEqualsWithDelta([346/360, 71/100, 45/100], Color::rgbToHsl(195, 33, 72), 0.01, "Bright Maroon");
        $this->assertEqualsWithDelta([330/360, 100/100, 50/100], Color::rgbToHsl(255, 0, 127), 0.01, "Bright Pink");
        $this->assertEqualsWithDelta([177/360, 93/100, 47/100], Color::rgbToHsl(8, 232, 222), 0.01, "Bright Turquoise");
        $this->assertEqualsWithDelta([281/360, 61/100, 77/100], Color::rgbToHsl(209, 159, 232), 0.01, "Bright Ube");
        $this->assertEqualsWithDelta([154/360, 100/100, 13/100], Color::rgbToHsl(0, 66, 37), 0.01, "British Racing Green");
        $this->assertEqualsWithDelta([30/360, 61/100, 50/100], Color::rgbToHsl(205, 127, 50), 0.01, "Bronze");
        $this->assertEqualsWithDelta([30/360, 100/100, 29/100], Color::rgbToHsl(150, 75, 0), 0.01, "Brown");
        $this->assertEqualsWithDelta([349/360, 100/100, 88/100], Color::rgbToHsl(255, 193, 204), 0.01, "Bubble Gum");
        $this->assertEqualsWithDelta([183/360, 100/100, 95/100], Color::rgbToHsl(231, 254, 255), 0.01, "Bubbles");
        $this->assertEqualsWithDelta([49/360, 79/100, 73/100], Color::rgbToHsl(240, 220, 130), 0.01, "Buff");
        $this->assertEqualsWithDelta([345/360, 100/100, 25/100], Color::rgbToHsl(128, 0, 32), 0.01, "Burgundy");
        $this->assertEqualsWithDelta([34/360, 57/100, 70/100], Color::rgbToHsl(222, 184, 135), 0.01, "Burlywood");
        $this->assertEqualsWithDelta([25/360, 100/100, 40/100], Color::rgbToHsl(204, 85, 0), 0.01, "Burnt Orange");
        $this->assertEqualsWithDelta([14/360, 78/100, 62/100], Color::rgbToHsl(233, 116, 81), 0.01, "Burnt Sienna");
        $this->assertEqualsWithDelta([9/360, 59/100, 34/100], Color::rgbToHsl(138, 51, 36), 0.01, "Burnt Umber");
        $this->assertEqualsWithDelta([311/360, 58/100, 47/100], Color::rgbToHsl(189, 51, 164), 0.01, "Byzantine");
        $this->assertEqualsWithDelta([311/360, 46/100, 30/100], Color::rgbToHsl(112, 41, 99), 0.01, "Byzantium");
        $this->assertEqualsWithDelta([206/360, 18/100, 40/100], Color::rgbToHsl(83, 104, 120), 0.01, "Cadet");
        $this->assertEqualsWithDelta([154/360, 100/100, 21/100], Color::rgbToHsl(0, 107, 60), 0.01, "Cadmium Green");
        $this->assertEqualsWithDelta([28/360, 84/100, 55/100], Color::rgbToHsl(237, 135, 45), 0.01, "Cadmium Orange");
        $this->assertEqualsWithDelta([351/360, 100/100, 45/100], Color::rgbToHsl(227, 0, 34), 0.01, "Cadmium Red");
        $this->assertEqualsWithDelta([140/360, 19/100, 70/100], Color::rgbToHsl(163, 193, 173), 0.01, "Cambridge Blue");
        $this->assertEqualsWithDelta([91/360, 11/100, 47/100], Color::rgbToHsl(120, 134, 107), 0.01, "Camouflage Green");
        $this->assertEqualsWithDelta([56/360, 100/100, 50/100], Color::rgbToHsl(255, 239, 0), 0.01, "Canary Yellow");
        $this->assertEqualsWithDelta([2/360, 100/100, 50/100], Color::rgbToHsl(255, 8, 0), 0.01, "Candy Apple Red");
        $this->assertEqualsWithDelta([350/360, 73/100, 44/100], Color::rgbToHsl(196, 30, 58), 0.01, "Cardinal");
        $this->assertEqualsWithDelta([165/360, 100/100, 40/100], Color::rgbToHsl(0, 204, 153), 0.01, "Caribbean Green");
        $this->assertEqualsWithDelta([350/360, 100/100, 29/100], Color::rgbToHsl(150, 0, 24), 0.01, "Carmine");
        $this->assertEqualsWithDelta([211/360, 50/100, 73/100], Color::rgbToHsl(153, 186, 221), 0.01, "Carolina Blue");
        $this->assertEqualsWithDelta([33/360, 85/100, 53/100], Color::rgbToHsl(237, 145, 33), 0.01, "Carrot Orange");
        $this->assertEqualsWithDelta([225/360, 39/100, 69/100], Color::rgbToHsl(146, 161, 207), 0.01, "Ceil");
        $this->assertEqualsWithDelta([123/360, 47/100, 78/100], Color::rgbToHsl(172, 225, 175), 0.01, "Celadon");
        $this->assertEqualsWithDelta([196/360, 100/100, 33/100], Color::rgbToHsl(0, 123, 167), 0.01, "Cerulean");
        $this->assertEqualsWithDelta([224/360, 64/100, 45/100], Color::rgbToHsl(42, 82, 190), 0.01, "Cerulean Blue");
        $this->assertEqualsWithDelta([26/360, 28/100, 49/100], Color::rgbToHsl(160, 120, 90), 0.01, "Chamoisee");
        $this->assertEqualsWithDelta([37/360, 72/100, 89/100], Color::rgbToHsl(247, 231, 206), 0.01, "Champagne");
        $this->assertEqualsWithDelta([204/360, 19/100, 26/100], Color::rgbToHsl(54, 69, 79), 0.01, "Charcoal");
        $this->assertEqualsWithDelta([68/360, 100/100, 50/100], Color::rgbToHsl(223, 255, 0), 0.01, "Chartreuse");
        $this->assertEqualsWithDelta([343/360, 72/100, 53/100], Color::rgbToHsl(222, 49, 99), 0.01, "Cherry");
        $this->assertEqualsWithDelta([348/360, 100/100, 86/100], Color::rgbToHsl(255, 183, 197), 0.01, "Cherry Blossom Pink");
        $this->assertEqualsWithDelta([0/360, 53/100, 58/100], Color::rgbToHsl(205, 92, 92), 0.01, "Chestnut");
        $this->assertEqualsWithDelta([31/360, 100/100, 24/100], Color::rgbToHsl(123, 63, 0), 0.01, "Chocolate");
        $this->assertEqualsWithDelta([39/360, 100/100, 50/100], Color::rgbToHsl(255, 167, 0), 0.01, "Chrome Yellow");
        $this->assertEqualsWithDelta([12/360, 12/100, 54/100], Color::rgbToHsl(152, 129, 123), 0.01, "Cinereous");
        $this->assertEqualsWithDelta([5/360, 76/100, 55/100], Color::rgbToHsl(227, 66, 52), 0.01, "Cinnabar");
        $this->assertEqualsWithDelta([25/360, 75/100, 47/100], Color::rgbToHsl(210, 105, 30), 0.01, "Cinnamon");
        $this->assertEqualsWithDelta([55/360, 92/100, 47/100], Color::rgbToHsl(228, 208, 10), 0.01, "Citrine");
        $this->assertEqualsWithDelta([326/360, 85/100, 89/100], Color::rgbToHsl(251, 204, 231), 0.01, "Classic Rose");
        $this->assertEqualsWithDelta([146/360, 100/100, 50/100], Color::rgbToHsl(0, 255, 111), 0.01, "Clover");
        $this->assertEqualsWithDelta([215/360, 100/100, 34/100], Color::rgbToHsl(0, 71, 171), 0.01, "Cobalt");
        $this->assertEqualsWithDelta([200/360, 100/100, 80/100], Color::rgbToHsl(155, 221, 255), 0.01, "Columbia Blue");
        $this->assertEqualsWithDelta([212/360, 100/100, 19/100], Color::rgbToHsl(0, 46, 99), 0.01, "Cool Black");
        $this->assertEqualsWithDelta([229/360, 16/100, 61/100], Color::rgbToHsl(140, 146, 172), 0.01, "Cool Grey");
        $this->assertEqualsWithDelta([29/360, 57/100, 46/100], Color::rgbToHsl(184, 115, 51), 0.01, "Copper");
        $this->assertEqualsWithDelta([13/360, 100/100, 50/100], Color::rgbToHsl(255, 56, 0), 0.01, "Coquelicot");
        $this->assertEqualsWithDelta([16/360, 100/100, 66/100], Color::rgbToHsl(255, 127, 80), 0.01, "Coral");
        $this->assertEqualsWithDelta([355/360, 37/100, 39/100], Color::rgbToHsl(137, 63, 69), 0.01, "Cordovan");
        $this->assertEqualsWithDelta([54/360, 95/100, 67/100], Color::rgbToHsl(251, 236, 93), 0.01, "Corn");
        $this->assertEqualsWithDelta([0/360, 74/100, 40/100], Color::rgbToHsl(179, 27, 27), 0.01, "Cornell Red");
        $this->assertEqualsWithDelta([219/360, 79/100, 66/100], Color::rgbToHsl(100, 149, 237), 0.01, "Cornflower Blue");
        $this->assertEqualsWithDelta([48/360, 100/100, 93/100], Color::rgbToHsl(255, 248, 220), 0.01, "Cornsilk");
        $this->assertEqualsWithDelta([57/360, 100/100, 91/100], Color::rgbToHsl(255, 253, 208), 0.01, "Cream");
        $this->assertEqualsWithDelta([348/360, 83/100, 47/100], Color::rgbToHsl(220, 20, 60), 0.01, "Crimson");
        $this->assertEqualsWithDelta([180/360, 100/100, 50/100], Color::rgbToHsl(0, 255, 255), 0.01, "Cyan");
        $this->assertEqualsWithDelta([60/360, 100/100, 60/100], Color::rgbToHsl(255, 255, 49), 0.01, "Daffodil");
        $this->assertEqualsWithDelta([55/360, 86/100, 56/100], Color::rgbToHsl(240, 225, 48), 0.01, "Dandelion");
        $this->assertEqualsWithDelta([240/360, 100/100, 27/100], Color::rgbToHsl(0, 0, 139), 0.01, "Dark Blue");
        $this->assertEqualsWithDelta([30/360, 51/100, 26/100], Color::rgbToHsl(101, 67, 33), 0.01, "Dark Brown");
        $this->assertEqualsWithDelta([315/360, 24/100, 29/100], Color::rgbToHsl(93, 57, 84), 0.01, "Dark Byzantium");
        $this->assertEqualsWithDelta([0/360, 100/100, 32/100], Color::rgbToHsl(164, 0, 0), 0.01, "Dark Candy Apple Red");
        $this->assertEqualsWithDelta([209/360, 88/100, 26/100], Color::rgbToHsl(8, 69, 126), 0.01, "Dark Cerulean");
        $this->assertEqualsWithDelta([10/360, 23/100, 49/100], Color::rgbToHsl(152, 105, 96), 0.01, "Dark Chestnut");
        $this->assertEqualsWithDelta([10/360, 58/100, 54/100], Color::rgbToHsl(205, 91, 69), 0.01, "Dark Coral");
        $this->assertEqualsWithDelta([180/360, 100/100, 27/100], Color::rgbToHsl(0, 139, 139), 0.01, "Dark Cyan");
        $this->assertEqualsWithDelta([43/360, 89/100, 38/100], Color::rgbToHsl(184, 134, 11), 0.01, "Dark Goldenrod");
        $this->assertEqualsWithDelta([158/360, 96/100, 10/100], Color::rgbToHsl(1, 50, 32), 0.01, "Dark Green");
        $this->assertEqualsWithDelta([162/360, 16/100, 12/100], Color::rgbToHsl(26, 36, 33), 0.01, "Dark Jungle Green");
        $this->assertEqualsWithDelta([56/360, 38/100, 58/100], Color::rgbToHsl(189, 183, 107), 0.01, "Dark Khaki");
        $this->assertEqualsWithDelta([27/360, 18/100, 24/100], Color::rgbToHsl(72, 60, 50), 0.01, "Dark Lava");
        $this->assertEqualsWithDelta([270/360, 31/100, 45/100], Color::rgbToHsl(115, 79, 150), 0.01, "Dark Lavender");
        $this->assertEqualsWithDelta([300/360, 100/100, 27/100], Color::rgbToHsl(139, 0, 139), 0.01, "Dark Magenta");
        $this->assertEqualsWithDelta([210/360, 100/100, 20/100], Color::rgbToHsl(0, 51, 102), 0.01, "Dark Midnight Blue");
        $this->assertEqualsWithDelta([82/360, 39/100, 30/100], Color::rgbToHsl(85, 107, 47), 0.01, "Dark Olive Green");
        $this->assertEqualsWithDelta([33/360, 100/100, 50/100], Color::rgbToHsl(255, 140, 0), 0.01, "Dark Orange");
        $this->assertEqualsWithDelta([212/360, 45/100, 63/100], Color::rgbToHsl(119, 158, 203), 0.01, "Dark Pastel Blue");
        $this->assertEqualsWithDelta([138/360, 97/100, 38/100], Color::rgbToHsl(3, 192, 60), 0.01, "Dark Pastel Green");
        $this->assertEqualsWithDelta([263/360, 56/100, 64/100], Color::rgbToHsl(150, 111, 214), 0.01, "Dark Pastel Purple");
        $this->assertEqualsWithDelta([9/360, 70/100, 45/100], Color::rgbToHsl(194, 59, 34), 0.01, "Dark Pastel Red");
        $this->assertEqualsWithDelta([342/360, 75/100, 62/100], Color::rgbToHsl(231, 84, 128), 0.01, "Dark Pink");
        $this->assertEqualsWithDelta([220/360, 100/100, 30/100], Color::rgbToHsl(0, 51, 153), 0.01, "Dark Powder Blue");
        $this->assertEqualsWithDelta([330/360, 56/100, 34/100], Color::rgbToHsl(135, 38, 87), 0.01, "Dark Raspberry");
        $this->assertEqualsWithDelta([15/360, 72/100, 70/100], Color::rgbToHsl(233, 150, 122), 0.01, "Dark Salmon");
        $this->assertEqualsWithDelta([344/360, 93/100, 17/100], Color::rgbToHsl(86, 3, 25), 0.01, "Dark Scarlet");
        $this->assertEqualsWithDelta([0/360, 50/100, 16/100], Color::rgbToHsl(60, 20, 20), 0.01, "Dark Sienna");
        $this->assertEqualsWithDelta([180/360, 25/100, 25/100], Color::rgbToHsl(47, 79, 79), 0.01, "Dark Slate Gray");
        $this->assertEqualsWithDelta([150/360, 66/100, 27/100], Color::rgbToHsl(23, 114, 69), 0.01, "Dark Spring Green");
        $this->assertEqualsWithDelta([45/360, 28/100, 44/100], Color::rgbToHsl(145, 129, 81), 0.01, "Dark Tan");
        $this->assertEqualsWithDelta([38/360, 100/100, 54/100], Color::rgbToHsl(255, 168, 18), 0.01, "Dark Tangerine");
        $this->assertEqualsWithDelta([353/360, 55/100, 55/100], Color::rgbToHsl(204, 78, 92), 0.01, "Dark Terra Cotta");
        $this->assertEqualsWithDelta([282/360, 100/100, 41/100], Color::rgbToHsl(148, 0, 211), 0.01, "Dark Violet");
        $this->assertEqualsWithDelta([0/360, 0/100, 33/100], Color::rgbToHsl(85, 85, 85), 0.01, "Davy'S Grey");
        $this->assertEqualsWithDelta([213/360, 80/100, 41/100], Color::rgbToHsl(21, 96, 189), 0.01, "Denim");
        $this->assertEqualsWithDelta([33/360, 41/100, 59/100], Color::rgbToHsl(193, 154, 107), 0.01, "Desert");
        $this->assertEqualsWithDelta([25/360, 63/100, 81/100], Color::rgbToHsl(237, 201, 175), 0.01, "Desert Sand");
        $this->assertEqualsWithDelta([0/360, 0/100, 41/100], Color::rgbToHsl(105, 105, 105), 0.01, "Dim Gray");
        $this->assertEqualsWithDelta([98/360, 39/100, 56/100], Color::rgbToHsl(133, 187, 101), 0.01, "Dollar Bill");
        $this->assertEqualsWithDelta([240/360, 100/100, 31/100], Color::rgbToHsl(0, 0, 156), 0.01, "Duke Blue");
        $this->assertEqualsWithDelta([34/360, 68/100, 63/100], Color::rgbToHsl(225, 169, 95), 0.01, "Earth Yellow");
        $this->assertEqualsWithDelta([329/360, 21/100, 32/100], Color::rgbToHsl(97, 64, 81), 0.01, "Eggplant");
        $this->assertEqualsWithDelta([140/360, 52/100, 55/100], Color::rgbToHsl(80, 200, 120), 0.01, "Emerald");
        $this->assertEqualsWithDelta([30/360, 69/100, 67/100], Color::rgbToHsl(229, 170, 112), 0.01, "Fawn");
        $this->assertEqualsWithDelta([7/360, 100/100, 50/100], Color::rgbToHsl(255, 28, 0), 0.01, "Ferrari Red");
        $this->assertEqualsWithDelta([357/360, 81/100, 45/100], Color::rgbToHsl(206, 22, 32), 0.01, "Fire Engine Red");
        $this->assertEqualsWithDelta([0/360, 68/100, 42/100], Color::rgbToHsl(178, 34, 34), 0.01, "Firebrick");
        $this->assertEqualsWithDelta([17/360, 77/100, 51/100], Color::rgbToHsl(226, 88, 34), 0.01, "Flame");
        $this->assertEqualsWithDelta([344/360, 95/100, 77/100], Color::rgbToHsl(252, 142, 172), 0.01, "Flamingo Pink");
        $this->assertEqualsWithDelta([52/360, 87/100, 76/100], Color::rgbToHsl(247, 233, 142), 0.01, "Flavescent");
        $this->assertEqualsWithDelta([149/360, 97/100, 14/100], Color::rgbToHsl(1, 68, 33), 0.01, "Forest Green");
        $this->assertEqualsWithDelta([39/360, 88/100, 48/100], Color::rgbToHsl(228, 155, 15), 0.01, "Gamboge");
        $this->assertEqualsWithDelta([240/360, 100/100, 99/100], Color::rgbToHsl(248, 248, 255), 0.01, "Ghost White");
        $this->assertEqualsWithDelta([216/360, 37/100, 55/100], Color::rgbToHsl(96, 130, 182), 0.01, "Glaucous");
        $this->assertEqualsWithDelta([36/360, 76/100, 34/100], Color::rgbToHsl(153, 101, 21), 0.01, "Golden Brown");
        $this->assertEqualsWithDelta([52/360, 100/100, 50/100], Color::rgbToHsl(255, 223, 0), 0.01, "Golden Yellow");
        $this->assertEqualsWithDelta([43/360, 74/100, 49/100], Color::rgbToHsl(218, 165, 32), 0.01, "Goldenrod");
        $this->assertEqualsWithDelta([0/360, 0/100, 50/100], Color::rgbToHsl(128, 128, 128), 0.01, "Gray");
        $this->assertEqualsWithDelta([120/360, 100/100, 25/100], Color::rgbToHsl(0, 128, 0), 0.01, "Green");
        $this->assertEqualsWithDelta([207/360, 52/100, 63/100], Color::rgbToHsl(113, 166, 210), 0.01, "Iceberg");
        $this->assertEqualsWithDelta([58/360, 96/100, 68/100], Color::rgbToHsl(252, 247, 94), 0.01, "Icterine");
        $this->assertEqualsWithDelta([84/360, 79/100, 65/100], Color::rgbToHsl(178, 236, 93), 0.01, "Inchworm");
        $this->assertEqualsWithDelta([115/360, 89/100, 28/100], Color::rgbToHsl(19, 136, 8), 0.01, "India Green");
        $this->assertEqualsWithDelta([0/360, 100/100, 68/100], Color::rgbToHsl(255, 92, 92), 0.01, "Indian Red");
        $this->assertEqualsWithDelta([35/360, 71/100, 62/100], Color::rgbToHsl(227, 168, 87), 0.01, "Indian Yellow");
        $this->assertEqualsWithDelta([223/360, 100/100, 33/100], Color::rgbToHsl(0, 47, 167), 0.01, "International Klein Blue");
        $this->assertEqualsWithDelta([60/360, 100/100, 97/100], Color::rgbToHsl(255, 255, 240), 0.01, "Ivory");
        $this->assertEqualsWithDelta([158/360, 100/100, 33/100], Color::rgbToHsl(0, 168, 107), 0.01, "Jade");
        $this->assertEqualsWithDelta([359/360, 66/100, 54/100], Color::rgbToHsl(215, 59, 62), 0.01, "Jasper");
        $this->assertEqualsWithDelta([37/360, 29/100, 67/100], Color::rgbToHsl(195, 176, 145), 0.01, "Khaki");
        $this->assertEqualsWithDelta([275/360, 57/100, 68/100], Color::rgbToHsl(181, 126, 220), 0.01, "Lavender");
        $this->assertEqualsWithDelta([240/360, 100/100, 90/100], Color::rgbToHsl(204, 204, 255), 0.01, "Lavender Blue");
        $this->assertEqualsWithDelta([340/360, 100/100, 97/100], Color::rgbToHsl(255, 240, 245), 0.01, "Lavender Blush");
        $this->assertEqualsWithDelta([245/360, 12/100, 79/100], Color::rgbToHsl(196, 195, 208), 0.01, "Lavender Gray");
        $this->assertEqualsWithDelta([90/360, 100/100, 49/100], Color::rgbToHsl(124, 252, 0), 0.01, "Lawn Green");
        $this->assertEqualsWithDelta([58/360, 100/100, 50/100], Color::rgbToHsl(255, 247, 0), 0.01, "Lemon");
        $this->assertEqualsWithDelta([75/360, 100/100, 50/100], Color::rgbToHsl(191, 255, 0), 0.01, "Lime");
        $this->assertEqualsWithDelta([300/360, 100/100, 50/100], Color::rgbToHsl(255, 0, 255), 0.01, "Magenta");
        $this->assertEqualsWithDelta([20/360, 100/100, 38/100], Color::rgbToHsl(192, 64, 0), 0.01, "Mahogany");
        $this->assertEqualsWithDelta([0/360, 100/100, 25/100], Color::rgbToHsl(128, 0, 0), 0.01, "Maroon");
        $this->assertEqualsWithDelta([240/360, 64/100, 27/100], Color::rgbToHsl(25, 25, 112), 0.01, "Midnight Blue");
        $this->assertEqualsWithDelta([158/360, 49/100, 47/100], Color::rgbToHsl(62, 180, 137), 0.01, "Mint");
        $this->assertEqualsWithDelta([47/360, 100/100, 67/100], Color::rgbToHsl(255, 219, 88), 0.01, "Mustard");
        $this->assertEqualsWithDelta([240/360, 100/100, 25/100], Color::rgbToHsl(0, 0, 128), 0.01, "Navy Blue");
        $this->assertEqualsWithDelta([30/360, 71/100, 47/100], Color::rgbToHsl(204, 119, 34), 0.01, "Ochre");
        $this->assertEqualsWithDelta([60/360, 100/100, 25/100], Color::rgbToHsl(128, 128, 0), 0.01, "Olive");
        $this->assertEqualsWithDelta([30/360, 100/100, 50/100], Color::rgbToHsl(255, 127, 0), 0.01, "Orange");
        $this->assertEqualsWithDelta([212/360, 100/100, 14/100], Color::rgbToHsl(0, 33, 71), 0.01, "Oxford Blue");
        $this->assertEqualsWithDelta([196/360, 26/100, 75/100], Color::rgbToHsl(174, 198, 207), 0.01, "Pastel Blue");
        $this->assertEqualsWithDelta([28/360, 22/100, 42/100], Color::rgbToHsl(131, 105, 83), 0.01, "Pastel Brown");
        $this->assertEqualsWithDelta([60/360, 10/100, 79/100], Color::rgbToHsl(207, 207, 196), 0.01, "Pastel Gray");
        $this->assertEqualsWithDelta([120/360, 60/100, 67/100], Color::rgbToHsl(119, 221, 119), 0.01, "Pastel Green");
        $this->assertEqualsWithDelta([333/360, 80/100, 78/100], Color::rgbToHsl(244, 154, 194), 0.01, "Pastel Magenta");
        $this->assertEqualsWithDelta([35/360, 100/100, 64/100], Color::rgbToHsl(255, 179, 71), 0.01, "Pastel Orange");
        $this->assertEqualsWithDelta([346/360, 100/100, 91/100], Color::rgbToHsl(255, 209, 220), 0.01, "Pastel Pink");
        $this->assertEqualsWithDelta([295/360, 13/100, 66/100], Color::rgbToHsl(179, 158, 181), 0.01, "Pastel Purple");
        $this->assertEqualsWithDelta([3/360, 100/100, 69/100], Color::rgbToHsl(255, 105, 97), 0.01, "Pastel Red");
        $this->assertEqualsWithDelta([302/360, 32/100, 70/100], Color::rgbToHsl(203, 153, 201), 0.01, "Pastel Violet");
        $this->assertEqualsWithDelta([60/360, 96/100, 79/100], Color::rgbToHsl(253, 253, 150), 0.01, "Pastel Yellow");
        $this->assertEqualsWithDelta([39/360, 100/100, 85/100], Color::rgbToHsl(255, 229, 180), 0.01, "Peach");
        $this->assertEqualsWithDelta([66/360, 75/100, 54/100], Color::rgbToHsl(209, 226, 49), 0.01, "Pear");
        $this->assertEqualsWithDelta([46/360, 46/100, 89/100], Color::rgbToHsl(240, 234, 214), 0.01, "Pearl");
        $this->assertEqualsWithDelta([59/360, 100/100, 45/100], Color::rgbToHsl(230, 226, 0), 0.01, "Peridot");
        $this->assertEqualsWithDelta([175/360, 98/100, 24/100], Color::rgbToHsl(1, 121, 111), 0.01, "Pine Green");
        $this->assertEqualsWithDelta([350/360, 100/100, 88/100], Color::rgbToHsl(255, 192, 203), 0.01, "Pink");
        $this->assertEqualsWithDelta([96/360, 42/100, 61/100], Color::rgbToHsl(147, 197, 114), 0.01, "Pistachio");
        $this->assertEqualsWithDelta([40/360, 5/100, 89/100], Color::rgbToHsl(229, 228, 226), 0.01, "Platinum");
        $this->assertEqualsWithDelta([307/360, 35/100, 41/100], Color::rgbToHsl(142, 69, 133), 0.01, "Plum");
        $this->assertEqualsWithDelta([11/360, 100/100, 61/100], Color::rgbToHsl(255, 90, 54), 0.01, "Portland Orange");
        $this->assertEqualsWithDelta([0/360, 60/100, 27/100], Color::rgbToHsl(112, 28, 28), 0.01, "Prune");
        $this->assertEqualsWithDelta([24/360, 100/100, 55/100], Color::rgbToHsl(255, 117, 24), 0.01, "Pumpkin");
        $this->assertEqualsWithDelta([270/360, 49/100, 41/100], Color::rgbToHsl(105, 53, 156), 0.01, "Purple Heart");
        $this->assertEqualsWithDelta([337/360, 91/100, 47/100], Color::rgbToHsl(227, 11, 93), 0.01, "Raspberry");
        $this->assertEqualsWithDelta([33/360, 31/100, 39/100], Color::rgbToHsl(130, 102, 68), 0.01, "Raw Umber");
        $this->assertEqualsWithDelta([0/360, 100/100, 50/100], Color::rgbToHsl(255, 0, 0), 0.01, "Red");
        $this->assertEqualsWithDelta([80/360, 17/100, 24/100], Color::rgbToHsl(65, 72, 51), 0.01, "Rifle Green");
        $this->assertEqualsWithDelta([353/360, 100/100, 20/100], Color::rgbToHsl(101, 0, 11), 0.01, "Rosewood");
        $this->assertEqualsWithDelta([219/360, 100/100, 20/100], Color::rgbToHsl(0, 35, 102), 0.01, "Royal Blue");
        $this->assertEqualsWithDelta([337/360, 86/100, 47/100], Color::rgbToHsl(224, 17, 95), 0.01, "Ruby");
        $this->assertEqualsWithDelta([18/360, 86/100, 39/100], Color::rgbToHsl(183, 65, 14), 0.01, "Rust");
        $this->assertEqualsWithDelta([24/360, 100/100, 50/100], Color::rgbToHsl(255, 103, 0), 0.01, "Safety Orange");
        $this->assertEqualsWithDelta([45/360, 90/100, 57/100], Color::rgbToHsl(244, 196, 48), 0.01, "Saffron");
        $this->assertEqualsWithDelta([14/360, 100/100, 71/100], Color::rgbToHsl(255, 140, 105), 0.01, "Salmon");
        $this->assertEqualsWithDelta([45/360, 35/100, 63/100], Color::rgbToHsl(194, 178, 128), 0.01, "Sand");
        $this->assertEqualsWithDelta([43/360, 73/100, 34/100], Color::rgbToHsl(150, 113, 23), 0.01, "Sand Dune");
        $this->assertEqualsWithDelta([52/360, 82/100, 59/100], Color::rgbToHsl(236, 213, 64), 0.01, "Sandstorm");
        $this->assertEqualsWithDelta([222/360, 86/100, 22/100], Color::rgbToHsl(8, 37, 103), 0.01, "Sapphire");
        $this->assertEqualsWithDelta([0/360, 43/100, 14/100], Color::rgbToHsl(50, 20, 20), 0.01, "Seal Brown");
        $this->assertEqualsWithDelta([25/360, 100/100, 97/100], Color::rgbToHsl(255, 245, 238), 0.01, "Seashell");
        $this->assertEqualsWithDelta([30/360, 70/100, 26/100], Color::rgbToHsl(112, 66, 20), 0.01, "Sepia");
        $this->assertEqualsWithDelta([37/360, 19/100, 45/100], Color::rgbToHsl(138, 121, 93), 0.01, "Shadow");
        $this->assertEqualsWithDelta([0/360, 0/100, 75/100], Color::rgbToHsl(192, 192, 192), 0.01, "Silver");
        $this->assertEqualsWithDelta([17/360, 90/100, 42/100], Color::rgbToHsl(203, 65, 11), 0.01, "Sinopia");
        $this->assertEqualsWithDelta([197/360, 71/100, 73/100], Color::rgbToHsl(135, 206, 235), 0.01, "Sky Blue");
        $this->assertEqualsWithDelta([320/360, 49/100, 63/100], Color::rgbToHsl(207, 113, 175), 0.01, "Sky Magenta");
        $this->assertEqualsWithDelta([0/360, 100/100, 99/100], Color::rgbToHsl(255, 250, 250), 0.01, "Snow");
        $this->assertEqualsWithDelta([80/360, 100/100, 49/100], Color::rgbToHsl(167, 252, 0), 0.01, "Spring Bud");
        $this->assertEqualsWithDelta([207/360, 44/100, 49/100], Color::rgbToHsl(70, 130, 180), 0.01, "Steel Blue");
        $this->assertEqualsWithDelta([54/360, 68/100, 66/100], Color::rgbToHsl(228, 217, 111), 0.01, "Straw");
        $this->assertEqualsWithDelta([35/360, 89/100, 81/100], Color::rgbToHsl(250, 214, 165), 0.01, "Sunset");
        $this->assertEqualsWithDelta([33/360, 100/100, 47/100], Color::rgbToHsl(242, 133, 0), 0.01, "Tangerine");
        $this->assertEqualsWithDelta([180/360, 100/100, 25/100], Color::rgbToHsl(0, 128, 128), 0.01, "Teal");
        $this->assertEqualsWithDelta([10/360, 70/100, 62/100], Color::rgbToHsl(226, 114, 91), 0.01, "Terra Cotta");
        $this->assertEqualsWithDelta([58/360, 100/100, 47/100], Color::rgbToHsl(238, 230, 0), 0.01, "Titanium Yellow");
        $this->assertEqualsWithDelta([168/360, 100/100, 23/100], Color::rgbToHsl(0, 117, 94), 0.01, "Tropical Rain Forest");
        $this->assertEqualsWithDelta([175/360, 66/100, 51/100], Color::rgbToHsl(48, 213, 200), 0.01, "Turquoise");
        $this->assertEqualsWithDelta([244/360, 87/100, 30/100], Color::rgbToHsl(18, 10, 143), 0.01, "Ultramarine");
        $this->assertEqualsWithDelta([216/360, 73/100, 63/100], Color::rgbToHsl(91, 146, 229), 0.01, "United Nations Blue");
        $this->assertEqualsWithDelta([48/360, 75/100, 81/100], Color::rgbToHsl(243, 229, 171), 0.01, "Vanilla");
        $this->assertEqualsWithDelta([274/360, 100/100, 50/100], Color::rgbToHsl(143, 0, 255), 0.01, "Violet");
        $this->assertEqualsWithDelta([39/360, 77/100, 83/100], Color::rgbToHsl(245, 222, 179), 0.01, "Wheat");
        $this->assertEqualsWithDelta([0/360, 0/100, 100/100], Color::rgbToHsl(255, 255, 255), 0.01, "White");
        $this->assertEqualsWithDelta([0/360, 0/100, 96/100], Color::rgbToHsl(245, 245, 245), 0.01, "White Smoke");
        $this->assertEqualsWithDelta([136/360, 8/100, 49/100], Color::rgbToHsl(115, 134, 120), 0.01, "Xanadu");
        $this->assertEqualsWithDelta([212/360, 81/100, 32/100], Color::rgbToHsl(15, 77, 146), 0.01, "Yale Blue");
        $this->assertEqualsWithDelta([60/360, 100/100, 50/100], Color::rgbToHsl(255, 255, 0), 0.01, "Yellow");
    }

    function testHslToRgb() {
        $this->assertSame([0, 0, 0], Color::hslToRgb(0, 0, 0));
        $this->assertSame([255, 255, 255], Color::hslToRgb(0, 0, 1));
        $this->assertSame([128, 128, 128], Color::hslToRgb(0, 0, .5));

        $this->assertSame([255, 0, 0], Color::hslToRgb(0/360, 1, .5));
        $this->assertSame([255, 255, 0], Color::hslToRgb(60/360, 1, .5));
        $this->assertSame([0, 255, 0], Color::hslToRgb(120/360, 1, .5));
        $this->assertSame([0, 255, 255], Color::hslToRgb(180/360, 1, .5));
        $this->assertSame([0, 0, 255], Color::hslToRgb(240/360, 1, .5));
        $this->assertSame([255, 0, 255], Color::hslToRgb(300/360, 1, .5));
        $this->assertSame([255, 0, 0], Color::hslToRgb(360/360, 1, .5));

        $this->assertEqualsWithDelta([240, 248, 255], Color::hslToRgb(208/360, 100/100, 97.1/100), 1, "aliceblue");
        $this->assertEqualsWithDelta([250, 235, 215], Color::hslToRgb(34/360, 77.8/100, 91.2/100), 1, "antiquewhite");
        $this->assertEqualsWithDelta([0, 255, 255], Color::hslToRgb(180/360, 100/100, 50/100), 1, "aqua");
        $this->assertEqualsWithDelta([127, 255, 212], Color::hslToRgb(160/360, 100/100, 74.9/100), 1, "aquamarine");
        $this->assertEqualsWithDelta([240, 255, 255], Color::hslToRgb(180/360, 100/100, 97.1/100), 1, "azure");
        $this->assertEqualsWithDelta([245, 245, 220], Color::hslToRgb(60/360, 55.6/100, 91.2/100), 1, "beige");
        $this->assertEqualsWithDelta([255, 228, 196], Color::hslToRgb(33/360, 100/100, 88.4/100), 1, "bisque");
        $this->assertEqualsWithDelta([0, 0, 0], Color::hslToRgb(0/360, 0/100, 0/100), 1, "black");
        $this->assertEqualsWithDelta([255, 235, 205], Color::hslToRgb(36/360, 100/100, 90.2/100), 1, "blanchedalmond");
        $this->assertEqualsWithDelta([0, 0, 255], Color::hslToRgb(240/360, 100/100, 50/100), 1, "blue");
        $this->assertEqualsWithDelta([138, 43, 226], Color::hslToRgb(271/360, 75.9/100, 52.7/100), 1, "blueviolet");
        $this->assertEqualsWithDelta([165, 42, 42], Color::hslToRgb(0/360, 59.4/100, 40.6/100), 1, "brown");
        $this->assertEqualsWithDelta([222, 184, 135], Color::hslToRgb(34/360, 56.9/100, 70/100), 1, "burlywood");
        $this->assertEqualsWithDelta([95, 158, 160], Color::hslToRgb(182/360, 25.5/100, 50/100), 1, "cadetblue");
        $this->assertEqualsWithDelta([127, 255, 0], Color::hslToRgb(90/360, 100/100, 50/100), 1, "chartreuse");
        $this->assertEqualsWithDelta([210, 105, 30], Color::hslToRgb(25/360, 75/100, 47.1/100), 1, "chocolate");
        $this->assertEqualsWithDelta([255, 127, 80], Color::hslToRgb(16/360, 100/100, 65.7/100), 1, "coral");
        $this->assertEqualsWithDelta([100, 149, 237], Color::hslToRgb(219/360, 79.2/100, 66.1/100), 1, "cornflowerblue");
        $this->assertEqualsWithDelta([255, 248, 220], Color::hslToRgb(48/360, 100/100, 93.1/100), 1, "cornsilk");
        $this->assertEqualsWithDelta([220, 20, 60], Color::hslToRgb(348/360, 83.3/100, 47.1/100), 1, "crimson");
        $this->assertEqualsWithDelta([0, 255, 255], Color::hslToRgb(180/360, 100/100, 50/100), 1, "cyan");
        $this->assertEqualsWithDelta([0, 0, 139], Color::hslToRgb(240/360, 100/100, 27.3/100), 1, "darkblue");
        $this->assertEqualsWithDelta([0, 139, 139], Color::hslToRgb(180/360, 100/100, 27.3/100), 1, "darkcyan");
        $this->assertEqualsWithDelta([184, 134, 11], Color::hslToRgb(43/360, 88.7/100, 38.2/100), 1, "darkgoldenrod");
        $this->assertEqualsWithDelta([169, 169, 169], Color::hslToRgb(0/360, 0/100, 66.3/100), 1, "darkgray");
        $this->assertEqualsWithDelta([0, 100, 0], Color::hslToRgb(120/360, 100/100, 19.6/100), 1, "darkgreen");
        $this->assertEqualsWithDelta([169, 169, 169], Color::hslToRgb(0/360, 0/100, 66.3/100), 1, "darkgrey");
        $this->assertEqualsWithDelta([189, 183, 107], Color::hslToRgb(56/360, 38.3/100, 58/100), 1, "darkkhaki");
        $this->assertEqualsWithDelta([139, 0, 139], Color::hslToRgb(300/360, 100/100, 27.3/100), 1, "darkmagenta");
        $this->assertEqualsWithDelta([85, 107, 47], Color::hslToRgb(82/360, 39/100, 30.2/100), 1, "darkolivegreen");
        $this->assertEqualsWithDelta([255, 140, 0], Color::hslToRgb(33/360, 100/100, 50/100), 1, "darkorange");
        $this->assertEqualsWithDelta([153, 50, 204], Color::hslToRgb(280/360, 60.6/100, 49.8/100), 1, "darkorchid");
        $this->assertEqualsWithDelta([139, 0, 0], Color::hslToRgb(0/360, 100/100, 27.3/100), 1, "darkred");
        $this->assertEqualsWithDelta([233, 150, 122], Color::hslToRgb(15/360, 71.6/100, 69.6/100), 1, "darksalmon");
        $this->assertEqualsWithDelta([143, 188, 143], Color::hslToRgb(120/360, 25.1/100, 64.9/100), 1, "darkseagreen");
        $this->assertEqualsWithDelta([72, 61, 139], Color::hslToRgb(248/360, 39/100, 39.2/100), 1, "darkslateblue");
        $this->assertEqualsWithDelta([47, 79, 79], Color::hslToRgb(180/360, 25.4/100, 24.7/100), 1, "darkslategray");
        $this->assertEqualsWithDelta([47, 79, 79], Color::hslToRgb(180/360, 25.4/100, 24.7/100), 1, "darkslategrey");
        $this->assertEqualsWithDelta([0, 206, 209], Color::hslToRgb(181/360, 100/100, 41/100), 1, "darkturquoise");
        $this->assertEqualsWithDelta([148, 0, 211], Color::hslToRgb(282/360, 100/100, 41.4/100), 1, "darkviolet");
        $this->assertEqualsWithDelta([255, 20, 145], Color::hslToRgb(328/360, 100/100, 53.9/100), 1, "deeppink");
        $this->assertEqualsWithDelta([0, 191, 255], Color::hslToRgb(195/360, 100/100, 50/100), 1, "deepskyblue");
        $this->assertEqualsWithDelta([105, 105, 105], Color::hslToRgb(0/360, 0/100, 41.2/100), 1, "dimgray");
        $this->assertEqualsWithDelta([105, 105, 105], Color::hslToRgb(0/360, 0/100, 41.2/100), 1, "dimgrey");
        $this->assertEqualsWithDelta([30, 144, 255], Color::hslToRgb(210/360, 100/100, 55.9/100), 1, "dodgerblue");
        $this->assertEqualsWithDelta([178, 34, 34], Color::hslToRgb(0/360, 67.9/100, 41.6/100), 1, "firebrick");
        $this->assertEqualsWithDelta([255, 250, 240], Color::hslToRgb(40/360, 100/100, 97.1/100), 1, "floralwhite");
        $this->assertEqualsWithDelta([34, 139, 34], Color::hslToRgb(120/360, 60.7/100, 33.9/100), 1, "forestgreen");
        $this->assertEqualsWithDelta([255, 0, 255], Color::hslToRgb(300/360, 100/100, 50/100), 1, "fuchsia");
        $this->assertEqualsWithDelta([220, 220, 220], Color::hslToRgb(0/360, 0/100, 86.3/100), 1, "gainsboro");
        $this->assertEqualsWithDelta([248, 248, 255], Color::hslToRgb(240/360, 100/100, 98.6/100), 1, "ghostwhite");
        $this->assertEqualsWithDelta([255, 217, 0], Color::hslToRgb(51/360, 100/100, 50/100), 1, "gold");
        $this->assertEqualsWithDelta([218, 165, 32], Color::hslToRgb(43/360, 74.4/100, 49/100), 1, "goldenrod");
        $this->assertEqualsWithDelta([128, 128, 128], Color::hslToRgb(0/360, 0/100, 50.2/100), 1, "gray");
        $this->assertEqualsWithDelta([0, 128, 0], Color::hslToRgb(120/360, 100/100, 25.1/100), 1, "green");
        $this->assertEqualsWithDelta([173, 255, 47], Color::hslToRgb(84/360, 100/100, 59.2/100), 1, "greenyellow");
        $this->assertEqualsWithDelta([128, 128, 128], Color::hslToRgb(0/360, 0/100, 50.2/100), 1, "grey");
        $this->assertEqualsWithDelta([240, 255, 240], Color::hslToRgb(120/360, 100/100, 97.1/100), 1, "honeydew");
        $this->assertEqualsWithDelta([255, 105, 180], Color::hslToRgb(330/360, 100/100, 70.6/100), 1, "hotpink");
        $this->assertEqualsWithDelta([205, 92, 92], Color::hslToRgb(0/360, 53.1/100, 58.2/100), 1, "indianred");
        $this->assertEqualsWithDelta([75, 0, 130], Color::hslToRgb(275/360, 100/100, 25.5/100), 1, "indigo");
        $this->assertEqualsWithDelta([255, 255, 240], Color::hslToRgb(60/360, 100/100, 97.1/100), 1, "ivory");
        $this->assertEqualsWithDelta([240, 230, 140], Color::hslToRgb(54/360, 76.9/100, 74.5/100), 1, "khaki");
        $this->assertEqualsWithDelta([230, 230, 250], Color::hslToRgb(240/360, 66.7/100, 94.1/100), 1, "lavender");
        $this->assertEqualsWithDelta([255, 240, 245], Color::hslToRgb(340/360, 100/100, 97.1/100), 1, "lavenderblush");
        $this->assertEqualsWithDelta([126, 252, 0], Color::hslToRgb(90/360, 100/100, 49.4/100), 1, "lawngreen");
        $this->assertEqualsWithDelta([255, 250, 205], Color::hslToRgb(54/360, 100/100, 90.2/100), 1, "lemonchiffon");
        $this->assertEqualsWithDelta([173, 216, 230], Color::hslToRgb(195/360, 53.3/100, 79/100), 1, "lightblue");
        $this->assertEqualsWithDelta([240, 128, 128], Color::hslToRgb(0/360, 78.9/100, 72.2/100), 1, "lightcoral");
        $this->assertEqualsWithDelta([224, 255, 255], Color::hslToRgb(180/360, 100/100, 93.9/100), 1, "lightcyan");
        $this->assertEqualsWithDelta([250, 250, 210], Color::hslToRgb(60/360, 80/100, 90.2/100), 1, "lightgoldenrodyellow");
        $this->assertEqualsWithDelta([211, 211, 211], Color::hslToRgb(0/360, 0/100, 82.7/100), 1, "lightgray");
        $this->assertEqualsWithDelta([144, 238, 144], Color::hslToRgb(120/360, 73.4/100, 74.9/100), 1, "lightgreen");
        $this->assertEqualsWithDelta([211, 211, 211], Color::hslToRgb(0/360, 0/100, 82.7/100), 1, "lightgrey");
        $this->assertEqualsWithDelta([255, 182, 193], Color::hslToRgb(351/360, 100/100, 85.7/100), 1, "lightpink");
        $this->assertEqualsWithDelta([255, 160, 122], Color::hslToRgb(17/360, 100/100, 73.9/100), 1, "lightsalmon");
        $this->assertEqualsWithDelta([32, 178, 170], Color::hslToRgb(177/360, 69.5/100, 41.2/100), 1, "lightseagreen");
        $this->assertEqualsWithDelta([135, 206, 250], Color::hslToRgb(203/360, 92/100, 75.5/100), 1, "lightskyblue");
        $this->assertEqualsWithDelta([119, 136, 153], Color::hslToRgb(210/360, 14.3/100, 53.3/100), 1, "lightslategray");
        $this->assertEqualsWithDelta([119, 136, 153], Color::hslToRgb(210/360, 14.3/100, 53.3/100), 1, "lightslategrey");
        $this->assertEqualsWithDelta([176, 196, 222], Color::hslToRgb(214/360, 41.1/100, 78/100), 1, "lightsteelblue");
        $this->assertEqualsWithDelta([255, 255, 224], Color::hslToRgb(60/360, 100/100, 93.9/100), 1, "lightyellow");
        $this->assertEqualsWithDelta([0, 255, 0], Color::hslToRgb(120/360, 100/100, 50/100), 1, "lime");
        $this->assertEqualsWithDelta([50, 205, 50], Color::hslToRgb(120/360, 60.8/100, 50/100), 1, "limegreen");
        $this->assertEqualsWithDelta([250, 240, 230], Color::hslToRgb(30/360, 66.7/100, 94.1/100), 1, "linen");
        $this->assertEqualsWithDelta([255, 0, 255], Color::hslToRgb(300/360, 100/100, 50/100), 1, "magenta");
        $this->assertEqualsWithDelta([128, 0, 0], Color::hslToRgb(0/360, 100/100, 25.1/100), 1, "maroon");
        $this->assertEqualsWithDelta([102, 205, 170], Color::hslToRgb(160/360, 50.7/100, 60.2/100), 1, "mediumaquamarine");
        $this->assertEqualsWithDelta([0, 0, 205], Color::hslToRgb(240/360, 100/100, 40.2/100), 1, "mediumblue");
        $this->assertEqualsWithDelta([186, 85, 211], Color::hslToRgb(288/360, 58.9/100, 58/100), 1, "mediumorchid");
        $this->assertEqualsWithDelta([147, 112, 219], Color::hslToRgb(260/360, 59.8/100, 64.9/100), 1, "mediumpurple");
        $this->assertEqualsWithDelta([60, 179, 113], Color::hslToRgb(147/360, 49.8/100, 46.9/100), 1, "mediumseagreen");
        $this->assertEqualsWithDelta([123, 104, 238], Color::hslToRgb(249/360, 79.8/100, 67.1/100), 1, "mediumslateblue");
        $this->assertEqualsWithDelta([0, 250, 154], Color::hslToRgb(157/360, 100/100, 49/100), 1, "mediumspringgreen");
        $this->assertEqualsWithDelta([72, 209, 204], Color::hslToRgb(178/360, 59.8/100, 55.1/100), 1, "mediumturquoise");
        $this->assertEqualsWithDelta([199, 21, 133], Color::hslToRgb(322/360, 80.9/100, 43.1/100), 1, "mediumvioletred");
        $this->assertEqualsWithDelta([25, 25, 112], Color::hslToRgb(240/360, 63.5/100, 26.9/100), 1, "midnightblue");
        $this->assertEqualsWithDelta([245, 255, 250], Color::hslToRgb(150/360, 100/100, 98/100), 1, "mintcream");
        $this->assertEqualsWithDelta([255, 228, 225], Color::hslToRgb(6/360, 100/100, 94.1/100), 1, "mistyrose");
        $this->assertEqualsWithDelta([255, 228, 181], Color::hslToRgb(38/360, 100/100, 85.5/100), 1, "moccasin");
        $this->assertEqualsWithDelta([255, 222, 173], Color::hslToRgb(36/360, 100/100, 83.9/100), 1, "navajowhite");
        $this->assertEqualsWithDelta([0, 0, 128], Color::hslToRgb(240/360, 100/100, 25.1/100), 1, "navy");
        $this->assertEqualsWithDelta([253, 245, 230], Color::hslToRgb(39/360, 85.2/100, 94.7/100), 1, "oldlace");
        $this->assertEqualsWithDelta([128, 128, 0], Color::hslToRgb(60/360, 100/100, 25.1/100), 1, "olive");
        $this->assertEqualsWithDelta([107, 142, 35], Color::hslToRgb(80/360, 60.5/100, 34.7/100), 1, "olivedrab");
        $this->assertEqualsWithDelta([255, 165, 0], Color::hslToRgb(39/360, 100/100, 50/100), 1, "orange");
        $this->assertEqualsWithDelta([255, 69, 0], Color::hslToRgb(16/360, 100/100, 50/100), 1, "orangered");
        $this->assertEqualsWithDelta([218, 112, 214], Color::hslToRgb(302/360, 58.9/100, 64.7/100), 1, "orchid");
        $this->assertEqualsWithDelta([238, 232, 170], Color::hslToRgb(55/360, 66.7/100, 80/100), 1, "palegoldenrod");
        $this->assertEqualsWithDelta([152, 251, 152], Color::hslToRgb(120/360, 92.5/100, 79/100), 1, "palegreen");
        $this->assertEqualsWithDelta([175, 238, 238], Color::hslToRgb(180/360, 64.9/100, 81/100), 1, "paleturquoise");
        $this->assertEqualsWithDelta([219, 112, 147], Color::hslToRgb(340/360, 59.8/100, 64.9/100), 1, "palevioletred");
        $this->assertEqualsWithDelta([255, 239, 213], Color::hslToRgb(37/360, 100/100, 91.8/100), 1, "papayawhip");
        $this->assertEqualsWithDelta([255, 218, 185], Color::hslToRgb(28/360, 100/100, 86.3/100), 1, "peachpuff");
        $this->assertEqualsWithDelta([205, 133, 63], Color::hslToRgb(30/360, 58.7/100, 52.5/100), 1, "peru");
        $this->assertEqualsWithDelta([255, 192, 203], Color::hslToRgb(350/360, 100/100, 87.6/100), 1, "pink");
        $this->assertEqualsWithDelta([221, 160, 221], Color::hslToRgb(300/360, 47.3/100, 74.7/100), 1, "plum");
        $this->assertEqualsWithDelta([176, 224, 230], Color::hslToRgb(187/360, 51.9/100, 79.6/100), 1, "powderblue");
        $this->assertEqualsWithDelta([128, 0, 128], Color::hslToRgb(300/360, 100/100, 25.1/100), 1, "purple");
        $this->assertEqualsWithDelta([102, 51, 153], Color::hslToRgb(270/360, 50/100, 40/100), 1, "rebeccapurple");
        $this->assertEqualsWithDelta([255, 0, 0], Color::hslToRgb(0/360, 100/100, 50/100), 1, "red");
        $this->assertEqualsWithDelta([188, 143, 143], Color::hslToRgb(0/360, 25.1/100, 64.9/100), 1, "rosybrown");
        $this->assertEqualsWithDelta([65, 105, 225], Color::hslToRgb(225/360, 72.7/100, 56.9/100), 1, "royalblue");
        $this->assertEqualsWithDelta([139, 69, 19], Color::hslToRgb(25/360, 75.9/100, 31/100), 1, "saddlebrown");
        $this->assertEqualsWithDelta([250, 128, 114], Color::hslToRgb(6/360, 93.2/100, 71.4/100), 1, "salmon");
        $this->assertEqualsWithDelta([244, 164, 96], Color::hslToRgb(28/360, 87.1/100, 66.7/100), 1, "sandybrown");
        $this->assertEqualsWithDelta([46, 139, 87], Color::hslToRgb(146/360, 50.3/100, 36.3/100), 1, "seagreen");
        $this->assertEqualsWithDelta([255, 245, 238], Color::hslToRgb(25/360, 100/100, 96.7/100), 1, "seashell");
        $this->assertEqualsWithDelta([160, 82, 45], Color::hslToRgb(19/360, 56.1/100, 40.2/100), 1, "sienna");
        $this->assertEqualsWithDelta([192, 192, 192], Color::hslToRgb(0/360, 0/100, 75.3/100), 1, "silver");
        $this->assertEqualsWithDelta([135, 206, 235], Color::hslToRgb(197/360, 71.4/100, 72.5/100), 1, "skyblue");
        $this->assertEqualsWithDelta([106, 90, 205], Color::hslToRgb(248/360, 53.5/100, 57.8/100), 1, "slateblue");
        $this->assertEqualsWithDelta([112, 128, 144], Color::hslToRgb(210/360, 12.6/100, 50.2/100), 1, "slategray");
        $this->assertEqualsWithDelta([112, 128, 144], Color::hslToRgb(210/360, 12.6/100, 50.2/100), 1, "slategrey");
        $this->assertEqualsWithDelta([255, 250, 250], Color::hslToRgb(0/360, 100/100, 99/100), 1, "snow");
        $this->assertEqualsWithDelta([0, 255, 127], Color::hslToRgb(150/360, 100/100, 50/100), 1, "springgreen");
        $this->assertEqualsWithDelta([70, 130, 180], Color::hslToRgb(207/360, 44/100, 49/100), 1, "steelblue");
        $this->assertEqualsWithDelta([210, 180, 140], Color::hslToRgb(34/360, 43.8/100, 68.6/100), 1, "tan");
        $this->assertEqualsWithDelta([0, 128, 128], Color::hslToRgb(180/360, 100/100, 25.1/100), 1, "teal");
        $this->assertEqualsWithDelta([216, 191, 216], Color::hslToRgb(300/360, 24.3/100, 79.8/100), 1, "thistle");
        $this->assertEqualsWithDelta([255, 99, 71], Color::hslToRgb(9/360, 100/100, 63.9/100), 1, "tomato");
        $this->assertEqualsWithDelta([64, 224, 208], Color::hslToRgb(174/360, 72.1/100, 56.5/100), 1, "turquoise");
        $this->assertEqualsWithDelta([238, 130, 238], Color::hslToRgb(300/360, 76.1/100, 72.2/100), 1, "violet");
        $this->assertEqualsWithDelta([245, 222, 179], Color::hslToRgb(39/360, 76.7/100, 83.1/100), 1, "wheat");
        $this->assertEqualsWithDelta([255, 255, 255], Color::hslToRgb(0/360, 0/100, 100/100), 1, "white");
        $this->assertEqualsWithDelta([245, 245, 245], Color::hslToRgb(0/360, 0/100, 96.1/100), 1, "whitesmoke");
        $this->assertEqualsWithDelta([255, 255, 0], Color::hslToRgb(60/360, 100/100, 50/100), 1, "yellow");
        $this->assertEqualsWithDelta([154, 205, 50], Color::hslToRgb(80/360, 60.8/100, 50/100), 1, "yellowgreen");
    }

    function testRgbToHslAndBack() {
        for($i = 0; $i < 100; ++$i) {
            $rgb = [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)];
            $hsl = Color::rgbToHsl($rgb[0], $rgb[1], $rgb[2]);
            $this->assertSame($rgb, Color::hslToRgb($hsl[0], $hsl[1], $hsl[2]), "rgb($rgb[0], $rgb[1], $rgb[2]) -> hsl($hsl[0], $hsl[1], $hsl[2])");
        }
    }

    function testHslToRgbAndBack() {
        for($i = 0; $i < 100; ++$i) {
            $hsl = [
                Math::randFloat(.1, .9), // avoid hues close to 0 and 1 because they can wrap around
                Math::randFloat(.1, 1), // avoid fully desaturated colors because they will change the hue to 0
                Math::randFloat(.1, .9) // full whites and full blacks can modify the hue and sat too
            ];
            $rgb = Color::hslToRgb($hsl[0], $hsl[1], $hsl[2]);
            $this->assertEqualsWithDelta($hsl, Color::rgbToHsl($rgb[0], $rgb[1], $rgb[2]), 0.05, "hsl($hsl[0], $hsl[1], $hsl[2]) -> rgb($rgb[0], $rgb[1], $rgb[2])");
        }
    }



    function testHuslToRgb() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEqualsWithDelta($colorspaces['rgb'], Color::huslToRgb(...$colorspaces['husl']), 1e-9, $hex);
        }
    }


    function testRgbToHusl() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEqualsWithDelta($colorspaces['husl'], Color::rgbToHusl(...$colorspaces['rgb']), 1e-9, $hex);
        }
    }

    function testHuslpToRgb() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEqualsWithDelta($colorspaces['rgb'], Color::huslpToRgb(...$colorspaces['huslp']), 1e-9, $hex);
        }
    }

    function testRgbToHuslp() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEqualsWithDelta($colorspaces['huslp'], Color::rgbToHuslp(...$colorspaces['rgb']), 1e-9, $hex);
        }
    }

    function testCssToInt() {
        $this->assertEquals(0x000000,Color::cssToInt('#000000'));
        $this->assertEquals(0xFF0000,Color::cssToInt('#FF0000'));
        $this->assertEquals(0x00FF00,Color::cssToInt('#00FF00'));
        $this->assertEquals(0x0000FF,Color::cssToInt('#0000FF'));
        $this->assertEquals(0xFFFF00,Color::cssToInt('#FFFF00'));
        $this->assertEquals(0x4080C0,Color::cssToInt('#4080C0'));

        $this->assertEquals(0x000000,Color::cssToInt('#000'));
        $this->assertEquals(0xFFFFFF,Color::cssToInt('#fff'));
        $this->assertEquals(0x4488CC,Color::cssToInt('#48c'));

        $this->assertEquals(0x4080C0,Color::cssToInt('rgb(64, 128, 192)'));
        $this->assertEquals(0x4080FF,Color::cssToInt('rgb(64, 128, 300)'));
        $this->assertEquals(0x64C832,Color::cssToInt('RgB( 100,200,  50 )'));

        $this->assertEquals(0x33336699,Color::cssToInt('rgba(51,102,153,0.8)'));
        if(PHP_INT_SIZE >= 8) {
            $this->assertEquals(0xCC336699, Color::cssToInt('rgba(51,102,153,0.2)'));
        }

        $this->assertEquals(0x14B814,Color::cssToInt('hsl(120,80%,40%)'));
        $this->assertEquals(0xDDDFF3,Color::cssToInt('hsl(237,46%,91%)'));

        $this->assertEquals(0x40DDDFF3,Color::cssToInt('hsla(237,46%,91%,0.75)'));

        $this->assertEquals(0x663399,Color::cssToInt('Rebecca Purple'));
        $this->assertEquals(0xFA8072,Color::cssToInt('SalMon'));
        $this->assertEquals(0xEEE8AA,Color::cssToInt('PaleGoldenrod'));
    }

    function testIntToCss() {
        $this->assertEquals('#000000',Color::intToCss(0x000000));
        $this->assertEquals('#ff0000',Color::intToCss(0xFF0000));
        $this->assertEquals('#00ff00',Color::intToCss(0x00FF00));
        $this->assertEquals('#0000ff',Color::intToCss(0x0000FF));
        $this->assertEquals('#ffff00',Color::intToCss(0xFFFF00));
        $this->assertEquals('#4080c0',Color::intToCss(0x4080C0));
        $this->assertEquals('rgba(255,255,255,0.502)',Color::intToCss(0x7fffffff));
    }

    function testIntToCssAndBack() {
        for($i=0; $i<1000; ++$i) {
            $in = mt_rand(0, min(PHP_INT_MAX, 0xFFffFFff));
            $css = Color::intToCss($in);
            $out = Color::cssToInt($css);
            $this->assertEquals($out,$in,sprintf('in: #%08X, out: #%08X', $in, $out));
        }
    }
}
