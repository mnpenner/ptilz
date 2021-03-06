<?php
use Ptilz\Color;
use Ptilz\Math;

class ColorTest extends PHPUnit_Framework_TestCase {

    static $huslTests;

    static function setUpBeforeClass() {
        self::$huslTests = \Ptilz\Json::loadFile(__DIR__ . '/husl-rev4.json');
    }

    function testRgbToHsl() {
        $this->assertSame([0., 0., 0.], Color::rgbToHsl(0, 0, 0));
        $this->assertSame([0., 0., 1.], Color::rgbToHsl(255, 255, 255));
        $this->assertSame([0., 0., .5], Color::rgbToHsl(127.5, 127.5, 127.5));
        $this->assertEquals([218/360, 1, .98], Color::rgbToHsl(244, 248, 255), 'aliceblue', 0.01);
        $this->assertEquals([37/360, 1., .5625], Color::rgbToHsl(255, 171, 32), 'local_closure_goog_color_color_test', 0.01);

        $this->assertSame([0/360., 1., .5], Color::rgbToHsl(255, 0, 0));
        $this->assertSame([60/360., 1., .5], Color::rgbToHsl(255, 255, 0));
        $this->assertSame([120/360., 1., .5], Color::rgbToHsl(0, 255, 0));
        $this->assertSame([180/360., 1., .5], Color::rgbToHsl(0, 255, 255));
        $this->assertSame([240/360., 1., .5], Color::rgbToHsl(0, 0, 255));
        $this->assertSame([300/360., 1., .5], Color::rgbToHsl(255, 0, 255));

        $this->assertEquals([204/360, 30/100, 51/100], Color::rgbToHsl(93, 138, 168), "Air Force Blue", 0.01);
        $this->assertEquals([208/360, 100/100, 97/100], Color::rgbToHsl(240, 248, 255), "Alice Blue", 0.01);
        $this->assertEquals([355/360, 77/100, 52/100], Color::rgbToHsl(227, 38, 54), "Alizarin", 0.01);
        $this->assertEquals([348/360, 78/100, 53/100], Color::rgbToHsl(229, 43, 80), "Amaranth", 0.01);
        $this->assertEquals([45/360, 100/100, 50/100], Color::rgbToHsl(255, 191, 0), "Amber", 0.01);
        $this->assertEquals([74/360, 55/100, 50/100], Color::rgbToHsl(164, 198, 57), "Android Green", 0.01);
        $this->assertEquals([74/360, 100/100, 36/100], Color::rgbToHsl(141, 182, 0), "Apple Green", 0.01);
        $this->assertEquals([24/360, 90/100, 84/100], Color::rgbToHsl(251, 206, 177), "Apricot", 0.01);
        $this->assertEquals([160/360, 100/100, 75/100], Color::rgbToHsl(127, 255, 212), "Aquamarine", 0.01);
        $this->assertEquals([69/360, 44/100, 23/100], Color::rgbToHsl(75, 83, 32), "Army Green", 0.01);
        $this->assertEquals([206/360, 12/100, 26/100], Color::rgbToHsl(59, 68, 75), "Arsenic", 0.01);
        $this->assertEquals([51/360, 74/100, 67/100], Color::rgbToHsl(233, 214, 107), "Arylide Yellow", 0.01);
        $this->assertEquals([135/360, 8/100, 72/100], Color::rgbToHsl(178, 190, 181), "Ash Grey", 0.01);
        $this->assertEquals([93/360, 27/100, 54/100], Color::rgbToHsl(135, 169, 107), "Asparagus", 0.01);
        $this->assertEquals([20/360, 100/100, 70/100], Color::rgbToHsl(255, 153, 102), "Atomic Tangerine", 0.01);
        $this->assertEquals([20/360, 61/100, 26/100], Color::rgbToHsl(109, 53, 26), "Auburn", 0.01);
        $this->assertEquals([210/360, 100/100, 50/100], Color::rgbToHsl(0, 127, 255), "Azure", 0.01);
        $this->assertEquals([199/360, 77/100, 74/100], Color::rgbToHsl(137, 207, 240), "Baby Blue", 0.01);
        $this->assertEquals([209/360, 74/100, 79/100], Color::rgbToHsl(161, 202, 241), "Baby Blue Eyes", 0.01);
        $this->assertEquals([0/360, 69/100, 86/100], Color::rgbToHsl(244, 194, 194), "Baby Pink", 0.01);
        $this->assertEquals([47/360, 100/100, 58/100], Color::rgbToHsl(255, 209, 42), "Banana Yellow", 0.01);
        $this->assertEquals([60/360, 1/100, 51/100], Color::rgbToHsl(132, 132, 130), "Battleship Grey", 0.01);
        $this->assertEquals([353/360, 14/100, 53/100], Color::rgbToHsl(152, 119, 123), "Bazaar", 0.01);
        $this->assertEquals([60/360, 56/100, 91/100], Color::rgbToHsl(245, 245, 220), "Beige", 0.01);
        $this->assertEquals([24/360, 33/100, 18/100], Color::rgbToHsl(61, 43, 31), "Bistre", 0.01);
        $this->assertEquals([0/360, 0/100, 0/100], Color::rgbToHsl(0, 0, 0), "Black", 0.01);
        $this->assertEquals([210/360, 79/100, 55/100], Color::rgbToHsl(49, 140, 231), "Bleu De France", 0.01);
        $this->assertEquals([50/360, 86/100, 86/100], Color::rgbToHsl(250, 240, 190), "Blond", 0.01);
        $this->assertEquals([240/360, 100/100, 50/100], Color::rgbToHsl(0, 0, 255), "Blue", 0.01);
        $this->assertEquals([342/360, 66/100, 62/100], Color::rgbToHsl(222, 93, 131), "Blush", 0.01);
        $this->assertEquals([9/360, 34/100, 35/100], Color::rgbToHsl(121, 68, 59), "Bole", 0.01);
        $this->assertEquals([0/360, 100/100, 40/100], Color::rgbToHsl(204, 0, 0), "Boston University Red", 0.01);
        $this->assertEquals([52/360, 47/100, 48/100], Color::rgbToHsl(181, 166, 66), "Brass", 0.01);
        $this->assertEquals([96/360, 100/100, 50/100], Color::rgbToHsl(102, 255, 0), "Bright Green", 0.01);
        $this->assertEquals([272/360, 60/100, 74/100], Color::rgbToHsl(191, 148, 228), "Bright Lavender", 0.01);
        $this->assertEquals([346/360, 71/100, 45/100], Color::rgbToHsl(195, 33, 72), "Bright Maroon", 0.01);
        $this->assertEquals([330/360, 100/100, 50/100], Color::rgbToHsl(255, 0, 127), "Bright Pink", 0.01);
        $this->assertEquals([177/360, 93/100, 47/100], Color::rgbToHsl(8, 232, 222), "Bright Turquoise", 0.01);
        $this->assertEquals([281/360, 61/100, 77/100], Color::rgbToHsl(209, 159, 232), "Bright Ube", 0.01);
        $this->assertEquals([154/360, 100/100, 13/100], Color::rgbToHsl(0, 66, 37), "British Racing Green", 0.01);
        $this->assertEquals([30/360, 61/100, 50/100], Color::rgbToHsl(205, 127, 50), "Bronze", 0.01);
        $this->assertEquals([30/360, 100/100, 29/100], Color::rgbToHsl(150, 75, 0), "Brown", 0.01);
        $this->assertEquals([349/360, 100/100, 88/100], Color::rgbToHsl(255, 193, 204), "Bubble Gum", 0.01);
        $this->assertEquals([183/360, 100/100, 95/100], Color::rgbToHsl(231, 254, 255), "Bubbles", 0.01);
        $this->assertEquals([49/360, 79/100, 73/100], Color::rgbToHsl(240, 220, 130), "Buff", 0.01);
        $this->assertEquals([345/360, 100/100, 25/100], Color::rgbToHsl(128, 0, 32), "Burgundy", 0.01);
        $this->assertEquals([34/360, 57/100, 70/100], Color::rgbToHsl(222, 184, 135), "Burlywood", 0.01);
        $this->assertEquals([25/360, 100/100, 40/100], Color::rgbToHsl(204, 85, 0), "Burnt Orange", 0.01);
        $this->assertEquals([14/360, 78/100, 62/100], Color::rgbToHsl(233, 116, 81), "Burnt Sienna", 0.01);
        $this->assertEquals([9/360, 59/100, 34/100], Color::rgbToHsl(138, 51, 36), "Burnt Umber", 0.01);
        $this->assertEquals([311/360, 58/100, 47/100], Color::rgbToHsl(189, 51, 164), "Byzantine", 0.01);
        $this->assertEquals([311/360, 46/100, 30/100], Color::rgbToHsl(112, 41, 99), "Byzantium", 0.01);
        $this->assertEquals([206/360, 18/100, 40/100], Color::rgbToHsl(83, 104, 120), "Cadet", 0.01);
        $this->assertEquals([154/360, 100/100, 21/100], Color::rgbToHsl(0, 107, 60), "Cadmium Green", 0.01);
        $this->assertEquals([28/360, 84/100, 55/100], Color::rgbToHsl(237, 135, 45), "Cadmium Orange", 0.01);
        $this->assertEquals([351/360, 100/100, 45/100], Color::rgbToHsl(227, 0, 34), "Cadmium Red", 0.01);
        $this->assertEquals([140/360, 19/100, 70/100], Color::rgbToHsl(163, 193, 173), "Cambridge Blue", 0.01);
        $this->assertEquals([91/360, 11/100, 47/100], Color::rgbToHsl(120, 134, 107), "Camouflage Green", 0.01);
        $this->assertEquals([56/360, 100/100, 50/100], Color::rgbToHsl(255, 239, 0), "Canary Yellow", 0.01);
        $this->assertEquals([2/360, 100/100, 50/100], Color::rgbToHsl(255, 8, 0), "Candy Apple Red", 0.01);
        $this->assertEquals([350/360, 73/100, 44/100], Color::rgbToHsl(196, 30, 58), "Cardinal", 0.01);
        $this->assertEquals([165/360, 100/100, 40/100], Color::rgbToHsl(0, 204, 153), "Caribbean Green", 0.01);
        $this->assertEquals([350/360, 100/100, 29/100], Color::rgbToHsl(150, 0, 24), "Carmine", 0.01);
        $this->assertEquals([211/360, 50/100, 73/100], Color::rgbToHsl(153, 186, 221), "Carolina Blue", 0.01);
        $this->assertEquals([33/360, 85/100, 53/100], Color::rgbToHsl(237, 145, 33), "Carrot Orange", 0.01);
        $this->assertEquals([225/360, 39/100, 69/100], Color::rgbToHsl(146, 161, 207), "Ceil", 0.01);
        $this->assertEquals([123/360, 47/100, 78/100], Color::rgbToHsl(172, 225, 175), "Celadon", 0.01);
        $this->assertEquals([196/360, 100/100, 33/100], Color::rgbToHsl(0, 123, 167), "Cerulean", 0.01);
        $this->assertEquals([224/360, 64/100, 45/100], Color::rgbToHsl(42, 82, 190), "Cerulean Blue", 0.01);
        $this->assertEquals([26/360, 28/100, 49/100], Color::rgbToHsl(160, 120, 90), "Chamoisee", 0.01);
        $this->assertEquals([37/360, 72/100, 89/100], Color::rgbToHsl(247, 231, 206), "Champagne", 0.01);
        $this->assertEquals([204/360, 19/100, 26/100], Color::rgbToHsl(54, 69, 79), "Charcoal", 0.01);
        $this->assertEquals([68/360, 100/100, 50/100], Color::rgbToHsl(223, 255, 0), "Chartreuse", 0.01);
        $this->assertEquals([343/360, 72/100, 53/100], Color::rgbToHsl(222, 49, 99), "Cherry", 0.01);
        $this->assertEquals([348/360, 100/100, 86/100], Color::rgbToHsl(255, 183, 197), "Cherry Blossom Pink", 0.01);
        $this->assertEquals([0/360, 53/100, 58/100], Color::rgbToHsl(205, 92, 92), "Chestnut", 0.01);
        $this->assertEquals([31/360, 100/100, 24/100], Color::rgbToHsl(123, 63, 0), "Chocolate", 0.01);
        $this->assertEquals([39/360, 100/100, 50/100], Color::rgbToHsl(255, 167, 0), "Chrome Yellow", 0.01);
        $this->assertEquals([12/360, 12/100, 54/100], Color::rgbToHsl(152, 129, 123), "Cinereous", 0.01);
        $this->assertEquals([5/360, 76/100, 55/100], Color::rgbToHsl(227, 66, 52), "Cinnabar", 0.01);
        $this->assertEquals([25/360, 75/100, 47/100], Color::rgbToHsl(210, 105, 30), "Cinnamon", 0.01);
        $this->assertEquals([55/360, 92/100, 47/100], Color::rgbToHsl(228, 208, 10), "Citrine", 0.01);
        $this->assertEquals([326/360, 85/100, 89/100], Color::rgbToHsl(251, 204, 231), "Classic Rose", 0.01);
        $this->assertEquals([146/360, 100/100, 50/100], Color::rgbToHsl(0, 255, 111), "Clover", 0.01);
        $this->assertEquals([215/360, 100/100, 34/100], Color::rgbToHsl(0, 71, 171), "Cobalt", 0.01);
        $this->assertEquals([200/360, 100/100, 80/100], Color::rgbToHsl(155, 221, 255), "Columbia Blue", 0.01);
        $this->assertEquals([212/360, 100/100, 19/100], Color::rgbToHsl(0, 46, 99), "Cool Black", 0.01);
        $this->assertEquals([229/360, 16/100, 61/100], Color::rgbToHsl(140, 146, 172), "Cool Grey", 0.01);
        $this->assertEquals([29/360, 57/100, 46/100], Color::rgbToHsl(184, 115, 51), "Copper", 0.01);
        $this->assertEquals([13/360, 100/100, 50/100], Color::rgbToHsl(255, 56, 0), "Coquelicot", 0.01);
        $this->assertEquals([16/360, 100/100, 66/100], Color::rgbToHsl(255, 127, 80), "Coral", 0.01);
        $this->assertEquals([355/360, 37/100, 39/100], Color::rgbToHsl(137, 63, 69), "Cordovan", 0.01);
        $this->assertEquals([54/360, 95/100, 67/100], Color::rgbToHsl(251, 236, 93), "Corn", 0.01);
        $this->assertEquals([0/360, 74/100, 40/100], Color::rgbToHsl(179, 27, 27), "Cornell Red", 0.01);
        $this->assertEquals([219/360, 79/100, 66/100], Color::rgbToHsl(100, 149, 237), "Cornflower Blue", 0.01);
        $this->assertEquals([48/360, 100/100, 93/100], Color::rgbToHsl(255, 248, 220), "Cornsilk", 0.01);
        $this->assertEquals([57/360, 100/100, 91/100], Color::rgbToHsl(255, 253, 208), "Cream", 0.01);
        $this->assertEquals([348/360, 83/100, 47/100], Color::rgbToHsl(220, 20, 60), "Crimson", 0.01);
        $this->assertEquals([180/360, 100/100, 50/100], Color::rgbToHsl(0, 255, 255), "Cyan", 0.01);
        $this->assertEquals([60/360, 100/100, 60/100], Color::rgbToHsl(255, 255, 49), "Daffodil", 0.01);
        $this->assertEquals([55/360, 86/100, 56/100], Color::rgbToHsl(240, 225, 48), "Dandelion", 0.01);
        $this->assertEquals([240/360, 100/100, 27/100], Color::rgbToHsl(0, 0, 139), "Dark Blue", 0.01);
        $this->assertEquals([30/360, 51/100, 26/100], Color::rgbToHsl(101, 67, 33), "Dark Brown", 0.01);
        $this->assertEquals([315/360, 24/100, 29/100], Color::rgbToHsl(93, 57, 84), "Dark Byzantium", 0.01);
        $this->assertEquals([0/360, 100/100, 32/100], Color::rgbToHsl(164, 0, 0), "Dark Candy Apple Red", 0.01);
        $this->assertEquals([209/360, 88/100, 26/100], Color::rgbToHsl(8, 69, 126), "Dark Cerulean", 0.01);
        $this->assertEquals([10/360, 23/100, 49/100], Color::rgbToHsl(152, 105, 96), "Dark Chestnut", 0.01);
        $this->assertEquals([10/360, 58/100, 54/100], Color::rgbToHsl(205, 91, 69), "Dark Coral", 0.01);
        $this->assertEquals([180/360, 100/100, 27/100], Color::rgbToHsl(0, 139, 139), "Dark Cyan", 0.01);
        $this->assertEquals([43/360, 89/100, 38/100], Color::rgbToHsl(184, 134, 11), "Dark Goldenrod", 0.01);
        $this->assertEquals([158/360, 96/100, 10/100], Color::rgbToHsl(1, 50, 32), "Dark Green", 0.01);
        $this->assertEquals([162/360, 16/100, 12/100], Color::rgbToHsl(26, 36, 33), "Dark Jungle Green", 0.01);
        $this->assertEquals([56/360, 38/100, 58/100], Color::rgbToHsl(189, 183, 107), "Dark Khaki", 0.01);
        $this->assertEquals([27/360, 18/100, 24/100], Color::rgbToHsl(72, 60, 50), "Dark Lava", 0.01);
        $this->assertEquals([270/360, 31/100, 45/100], Color::rgbToHsl(115, 79, 150), "Dark Lavender", 0.01);
        $this->assertEquals([300/360, 100/100, 27/100], Color::rgbToHsl(139, 0, 139), "Dark Magenta", 0.01);
        $this->assertEquals([210/360, 100/100, 20/100], Color::rgbToHsl(0, 51, 102), "Dark Midnight Blue", 0.01);
        $this->assertEquals([82/360, 39/100, 30/100], Color::rgbToHsl(85, 107, 47), "Dark Olive Green", 0.01);
        $this->assertEquals([33/360, 100/100, 50/100], Color::rgbToHsl(255, 140, 0), "Dark Orange", 0.01);
        $this->assertEquals([212/360, 45/100, 63/100], Color::rgbToHsl(119, 158, 203), "Dark Pastel Blue", 0.01);
        $this->assertEquals([138/360, 97/100, 38/100], Color::rgbToHsl(3, 192, 60), "Dark Pastel Green", 0.01);
        $this->assertEquals([263/360, 56/100, 64/100], Color::rgbToHsl(150, 111, 214), "Dark Pastel Purple", 0.01);
        $this->assertEquals([9/360, 70/100, 45/100], Color::rgbToHsl(194, 59, 34), "Dark Pastel Red", 0.01);
        $this->assertEquals([342/360, 75/100, 62/100], Color::rgbToHsl(231, 84, 128), "Dark Pink", 0.01);
        $this->assertEquals([220/360, 100/100, 30/100], Color::rgbToHsl(0, 51, 153), "Dark Powder Blue", 0.01);
        $this->assertEquals([330/360, 56/100, 34/100], Color::rgbToHsl(135, 38, 87), "Dark Raspberry", 0.01);
        $this->assertEquals([15/360, 72/100, 70/100], Color::rgbToHsl(233, 150, 122), "Dark Salmon", 0.01);
        $this->assertEquals([344/360, 93/100, 17/100], Color::rgbToHsl(86, 3, 25), "Dark Scarlet", 0.01);
        $this->assertEquals([0/360, 50/100, 16/100], Color::rgbToHsl(60, 20, 20), "Dark Sienna", 0.01);
        $this->assertEquals([180/360, 25/100, 25/100], Color::rgbToHsl(47, 79, 79), "Dark Slate Gray", 0.01);
        $this->assertEquals([150/360, 66/100, 27/100], Color::rgbToHsl(23, 114, 69), "Dark Spring Green", 0.01);
        $this->assertEquals([45/360, 28/100, 44/100], Color::rgbToHsl(145, 129, 81), "Dark Tan", 0.01);
        $this->assertEquals([38/360, 100/100, 54/100], Color::rgbToHsl(255, 168, 18), "Dark Tangerine", 0.01);
        $this->assertEquals([353/360, 55/100, 55/100], Color::rgbToHsl(204, 78, 92), "Dark Terra Cotta", 0.01);
        $this->assertEquals([282/360, 100/100, 41/100], Color::rgbToHsl(148, 0, 211), "Dark Violet", 0.01);
        $this->assertEquals([0/360, 0/100, 33/100], Color::rgbToHsl(85, 85, 85), "Davy'S Grey", 0.01);
        $this->assertEquals([213/360, 80/100, 41/100], Color::rgbToHsl(21, 96, 189), "Denim", 0.01);
        $this->assertEquals([33/360, 41/100, 59/100], Color::rgbToHsl(193, 154, 107), "Desert", 0.01);
        $this->assertEquals([25/360, 63/100, 81/100], Color::rgbToHsl(237, 201, 175), "Desert Sand", 0.01);
        $this->assertEquals([0/360, 0/100, 41/100], Color::rgbToHsl(105, 105, 105), "Dim Gray", 0.01);
        $this->assertEquals([98/360, 39/100, 56/100], Color::rgbToHsl(133, 187, 101), "Dollar Bill", 0.01);
        $this->assertEquals([240/360, 100/100, 31/100], Color::rgbToHsl(0, 0, 156), "Duke Blue", 0.01);
        $this->assertEquals([34/360, 68/100, 63/100], Color::rgbToHsl(225, 169, 95), "Earth Yellow", 0.01);
        $this->assertEquals([329/360, 21/100, 32/100], Color::rgbToHsl(97, 64, 81), "Eggplant", 0.01);
        $this->assertEquals([140/360, 52/100, 55/100], Color::rgbToHsl(80, 200, 120), "Emerald", 0.01);
        $this->assertEquals([30/360, 69/100, 67/100], Color::rgbToHsl(229, 170, 112), "Fawn", 0.01);
        $this->assertEquals([7/360, 100/100, 50/100], Color::rgbToHsl(255, 28, 0), "Ferrari Red", 0.01);
        $this->assertEquals([357/360, 81/100, 45/100], Color::rgbToHsl(206, 22, 32), "Fire Engine Red", 0.01);
        $this->assertEquals([0/360, 68/100, 42/100], Color::rgbToHsl(178, 34, 34), "Firebrick", 0.01);
        $this->assertEquals([17/360, 77/100, 51/100], Color::rgbToHsl(226, 88, 34), "Flame", 0.01);
        $this->assertEquals([344/360, 95/100, 77/100], Color::rgbToHsl(252, 142, 172), "Flamingo Pink", 0.01);
        $this->assertEquals([52/360, 87/100, 76/100], Color::rgbToHsl(247, 233, 142), "Flavescent", 0.01);
        $this->assertEquals([149/360, 97/100, 14/100], Color::rgbToHsl(1, 68, 33), "Forest Green", 0.01);
        $this->assertEquals([39/360, 88/100, 48/100], Color::rgbToHsl(228, 155, 15), "Gamboge", 0.01);
        $this->assertEquals([240/360, 100/100, 99/100], Color::rgbToHsl(248, 248, 255), "Ghost White", 0.01);
        $this->assertEquals([216/360, 37/100, 55/100], Color::rgbToHsl(96, 130, 182), "Glaucous", 0.01);
        $this->assertEquals([36/360, 76/100, 34/100], Color::rgbToHsl(153, 101, 21), "Golden Brown", 0.01);
        $this->assertEquals([52/360, 100/100, 50/100], Color::rgbToHsl(255, 223, 0), "Golden Yellow", 0.01);
        $this->assertEquals([43/360, 74/100, 49/100], Color::rgbToHsl(218, 165, 32), "Goldenrod", 0.01);
        $this->assertEquals([0/360, 0/100, 50/100], Color::rgbToHsl(128, 128, 128), "Gray", 0.01);
        $this->assertEquals([120/360, 100/100, 25/100], Color::rgbToHsl(0, 128, 0), "Green", 0.01);
        $this->assertEquals([207/360, 52/100, 63/100], Color::rgbToHsl(113, 166, 210), "Iceberg", 0.01);
        $this->assertEquals([58/360, 96/100, 68/100], Color::rgbToHsl(252, 247, 94), "Icterine", 0.01);
        $this->assertEquals([84/360, 79/100, 65/100], Color::rgbToHsl(178, 236, 93), "Inchworm", 0.01);
        $this->assertEquals([115/360, 89/100, 28/100], Color::rgbToHsl(19, 136, 8), "India Green", 0.01);
        $this->assertEquals([0/360, 100/100, 68/100], Color::rgbToHsl(255, 92, 92), "Indian Red", 0.01);
        $this->assertEquals([35/360, 71/100, 62/100], Color::rgbToHsl(227, 168, 87), "Indian Yellow", 0.01);
        $this->assertEquals([223/360, 100/100, 33/100], Color::rgbToHsl(0, 47, 167), "International Klein Blue", 0.01);
        $this->assertEquals([60/360, 100/100, 97/100], Color::rgbToHsl(255, 255, 240), "Ivory", 0.01);
        $this->assertEquals([158/360, 100/100, 33/100], Color::rgbToHsl(0, 168, 107), "Jade", 0.01);
        $this->assertEquals([359/360, 66/100, 54/100], Color::rgbToHsl(215, 59, 62), "Jasper", 0.01);
        $this->assertEquals([37/360, 29/100, 67/100], Color::rgbToHsl(195, 176, 145), "Khaki", 0.01);
        $this->assertEquals([275/360, 57/100, 68/100], Color::rgbToHsl(181, 126, 220), "Lavender", 0.01);
        $this->assertEquals([240/360, 100/100, 90/100], Color::rgbToHsl(204, 204, 255), "Lavender Blue", 0.01);
        $this->assertEquals([340/360, 100/100, 97/100], Color::rgbToHsl(255, 240, 245), "Lavender Blush", 0.01);
        $this->assertEquals([245/360, 12/100, 79/100], Color::rgbToHsl(196, 195, 208), "Lavender Gray", 0.01);
        $this->assertEquals([90/360, 100/100, 49/100], Color::rgbToHsl(124, 252, 0), "Lawn Green", 0.01);
        $this->assertEquals([58/360, 100/100, 50/100], Color::rgbToHsl(255, 247, 0), "Lemon", 0.01);
        $this->assertEquals([75/360, 100/100, 50/100], Color::rgbToHsl(191, 255, 0), "Lime", 0.01);
        $this->assertEquals([300/360, 100/100, 50/100], Color::rgbToHsl(255, 0, 255), "Magenta", 0.01);
        $this->assertEquals([20/360, 100/100, 38/100], Color::rgbToHsl(192, 64, 0), "Mahogany", 0.01);
        $this->assertEquals([0/360, 100/100, 25/100], Color::rgbToHsl(128, 0, 0), "Maroon", 0.01);
        $this->assertEquals([240/360, 64/100, 27/100], Color::rgbToHsl(25, 25, 112), "Midnight Blue", 0.01);
        $this->assertEquals([158/360, 49/100, 47/100], Color::rgbToHsl(62, 180, 137), "Mint", 0.01);
        $this->assertEquals([47/360, 100/100, 67/100], Color::rgbToHsl(255, 219, 88), "Mustard", 0.01);
        $this->assertEquals([240/360, 100/100, 25/100], Color::rgbToHsl(0, 0, 128), "Navy Blue", 0.01);
        $this->assertEquals([30/360, 71/100, 47/100], Color::rgbToHsl(204, 119, 34), "Ochre", 0.01);
        $this->assertEquals([60/360, 100/100, 25/100], Color::rgbToHsl(128, 128, 0), "Olive", 0.01);
        $this->assertEquals([30/360, 100/100, 50/100], Color::rgbToHsl(255, 127, 0), "Orange", 0.01);
        $this->assertEquals([212/360, 100/100, 14/100], Color::rgbToHsl(0, 33, 71), "Oxford Blue", 0.01);
        $this->assertEquals([196/360, 26/100, 75/100], Color::rgbToHsl(174, 198, 207), "Pastel Blue", 0.01);
        $this->assertEquals([28/360, 22/100, 42/100], Color::rgbToHsl(131, 105, 83), "Pastel Brown", 0.01);
        $this->assertEquals([60/360, 10/100, 79/100], Color::rgbToHsl(207, 207, 196), "Pastel Gray", 0.01);
        $this->assertEquals([120/360, 60/100, 67/100], Color::rgbToHsl(119, 221, 119), "Pastel Green", 0.01);
        $this->assertEquals([333/360, 80/100, 78/100], Color::rgbToHsl(244, 154, 194), "Pastel Magenta", 0.01);
        $this->assertEquals([35/360, 100/100, 64/100], Color::rgbToHsl(255, 179, 71), "Pastel Orange", 0.01);
        $this->assertEquals([346/360, 100/100, 91/100], Color::rgbToHsl(255, 209, 220), "Pastel Pink", 0.01);
        $this->assertEquals([295/360, 13/100, 66/100], Color::rgbToHsl(179, 158, 181), "Pastel Purple", 0.01);
        $this->assertEquals([3/360, 100/100, 69/100], Color::rgbToHsl(255, 105, 97), "Pastel Red", 0.01);
        $this->assertEquals([302/360, 32/100, 70/100], Color::rgbToHsl(203, 153, 201), "Pastel Violet", 0.01);
        $this->assertEquals([60/360, 96/100, 79/100], Color::rgbToHsl(253, 253, 150), "Pastel Yellow", 0.01);
        $this->assertEquals([39/360, 100/100, 85/100], Color::rgbToHsl(255, 229, 180), "Peach", 0.01);
        $this->assertEquals([66/360, 75/100, 54/100], Color::rgbToHsl(209, 226, 49), "Pear", 0.01);
        $this->assertEquals([46/360, 46/100, 89/100], Color::rgbToHsl(240, 234, 214), "Pearl", 0.01);
        $this->assertEquals([59/360, 100/100, 45/100], Color::rgbToHsl(230, 226, 0), "Peridot", 0.01);
        $this->assertEquals([175/360, 98/100, 24/100], Color::rgbToHsl(1, 121, 111), "Pine Green", 0.01);
        $this->assertEquals([350/360, 100/100, 88/100], Color::rgbToHsl(255, 192, 203), "Pink", 0.01);
        $this->assertEquals([96/360, 42/100, 61/100], Color::rgbToHsl(147, 197, 114), "Pistachio", 0.01);
        $this->assertEquals([40/360, 5/100, 89/100], Color::rgbToHsl(229, 228, 226), "Platinum", 0.01);
        $this->assertEquals([307/360, 35/100, 41/100], Color::rgbToHsl(142, 69, 133), "Plum", 0.01);
        $this->assertEquals([11/360, 100/100, 61/100], Color::rgbToHsl(255, 90, 54), "Portland Orange", 0.01);
        $this->assertEquals([0/360, 60/100, 27/100], Color::rgbToHsl(112, 28, 28), "Prune", 0.01);
        $this->assertEquals([24/360, 100/100, 55/100], Color::rgbToHsl(255, 117, 24), "Pumpkin", 0.01);
        $this->assertEquals([270/360, 49/100, 41/100], Color::rgbToHsl(105, 53, 156), "Purple Heart", 0.01);
        $this->assertEquals([337/360, 91/100, 47/100], Color::rgbToHsl(227, 11, 93), "Raspberry", 0.01);
        $this->assertEquals([33/360, 31/100, 39/100], Color::rgbToHsl(130, 102, 68), "Raw Umber", 0.01);
        $this->assertEquals([0/360, 100/100, 50/100], Color::rgbToHsl(255, 0, 0), "Red", 0.01);
        $this->assertEquals([80/360, 17/100, 24/100], Color::rgbToHsl(65, 72, 51), "Rifle Green", 0.01);
        $this->assertEquals([353/360, 100/100, 20/100], Color::rgbToHsl(101, 0, 11), "Rosewood", 0.01);
        $this->assertEquals([219/360, 100/100, 20/100], Color::rgbToHsl(0, 35, 102), "Royal Blue", 0.01);
        $this->assertEquals([337/360, 86/100, 47/100], Color::rgbToHsl(224, 17, 95), "Ruby", 0.01);
        $this->assertEquals([18/360, 86/100, 39/100], Color::rgbToHsl(183, 65, 14), "Rust", 0.01);
        $this->assertEquals([24/360, 100/100, 50/100], Color::rgbToHsl(255, 103, 0), "Safety Orange", 0.01);
        $this->assertEquals([45/360, 90/100, 57/100], Color::rgbToHsl(244, 196, 48), "Saffron", 0.01);
        $this->assertEquals([14/360, 100/100, 71/100], Color::rgbToHsl(255, 140, 105), "Salmon", 0.01);
        $this->assertEquals([45/360, 35/100, 63/100], Color::rgbToHsl(194, 178, 128), "Sand", 0.01);
        $this->assertEquals([43/360, 73/100, 34/100], Color::rgbToHsl(150, 113, 23), "Sand Dune", 0.01);
        $this->assertEquals([52/360, 82/100, 59/100], Color::rgbToHsl(236, 213, 64), "Sandstorm", 0.01);
        $this->assertEquals([222/360, 86/100, 22/100], Color::rgbToHsl(8, 37, 103), "Sapphire", 0.01);
        $this->assertEquals([0/360, 43/100, 14/100], Color::rgbToHsl(50, 20, 20), "Seal Brown", 0.01);
        $this->assertEquals([25/360, 100/100, 97/100], Color::rgbToHsl(255, 245, 238), "Seashell", 0.01);
        $this->assertEquals([30/360, 70/100, 26/100], Color::rgbToHsl(112, 66, 20), "Sepia", 0.01);
        $this->assertEquals([37/360, 19/100, 45/100], Color::rgbToHsl(138, 121, 93), "Shadow", 0.01);
        $this->assertEquals([0/360, 0/100, 75/100], Color::rgbToHsl(192, 192, 192), "Silver", 0.01);
        $this->assertEquals([17/360, 90/100, 42/100], Color::rgbToHsl(203, 65, 11), "Sinopia", 0.01);
        $this->assertEquals([197/360, 71/100, 73/100], Color::rgbToHsl(135, 206, 235), "Sky Blue", 0.01);
        $this->assertEquals([320/360, 49/100, 63/100], Color::rgbToHsl(207, 113, 175), "Sky Magenta", 0.01);
        $this->assertEquals([0/360, 100/100, 99/100], Color::rgbToHsl(255, 250, 250), "Snow", 0.01);
        $this->assertEquals([80/360, 100/100, 49/100], Color::rgbToHsl(167, 252, 0), "Spring Bud", 0.01);
        $this->assertEquals([207/360, 44/100, 49/100], Color::rgbToHsl(70, 130, 180), "Steel Blue", 0.01);
        $this->assertEquals([54/360, 68/100, 66/100], Color::rgbToHsl(228, 217, 111), "Straw", 0.01);
        $this->assertEquals([35/360, 89/100, 81/100], Color::rgbToHsl(250, 214, 165), "Sunset", 0.01);
        $this->assertEquals([33/360, 100/100, 47/100], Color::rgbToHsl(242, 133, 0), "Tangerine", 0.01);
        $this->assertEquals([180/360, 100/100, 25/100], Color::rgbToHsl(0, 128, 128), "Teal", 0.01);
        $this->assertEquals([10/360, 70/100, 62/100], Color::rgbToHsl(226, 114, 91), "Terra Cotta", 0.01);
        $this->assertEquals([58/360, 100/100, 47/100], Color::rgbToHsl(238, 230, 0), "Titanium Yellow", 0.01);
        $this->assertEquals([168/360, 100/100, 23/100], Color::rgbToHsl(0, 117, 94), "Tropical Rain Forest", 0.01);
        $this->assertEquals([175/360, 66/100, 51/100], Color::rgbToHsl(48, 213, 200), "Turquoise", 0.01);
        $this->assertEquals([244/360, 87/100, 30/100], Color::rgbToHsl(18, 10, 143), "Ultramarine", 0.01);
        $this->assertEquals([216/360, 73/100, 63/100], Color::rgbToHsl(91, 146, 229), "United Nations Blue", 0.01);
        $this->assertEquals([48/360, 75/100, 81/100], Color::rgbToHsl(243, 229, 171), "Vanilla", 0.01);
        $this->assertEquals([274/360, 100/100, 50/100], Color::rgbToHsl(143, 0, 255), "Violet", 0.01);
        $this->assertEquals([39/360, 77/100, 83/100], Color::rgbToHsl(245, 222, 179), "Wheat", 0.01);
        $this->assertEquals([0/360, 0/100, 100/100], Color::rgbToHsl(255, 255, 255), "White", 0.01);
        $this->assertEquals([0/360, 0/100, 96/100], Color::rgbToHsl(245, 245, 245), "White Smoke", 0.01);
        $this->assertEquals([136/360, 8/100, 49/100], Color::rgbToHsl(115, 134, 120), "Xanadu", 0.01);
        $this->assertEquals([212/360, 81/100, 32/100], Color::rgbToHsl(15, 77, 146), "Yale Blue", 0.01);
        $this->assertEquals([60/360, 100/100, 50/100], Color::rgbToHsl(255, 255, 0), "Yellow", 0.01);
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

        $this->assertEquals([240, 248, 255], Color::hslToRgb(208/360, 100/100, 97.1/100), "aliceblue", 1);
        $this->assertEquals([250, 235, 215], Color::hslToRgb(34/360, 77.8/100, 91.2/100), "antiquewhite", 1);
        $this->assertEquals([0, 255, 255], Color::hslToRgb(180/360, 100/100, 50/100), "aqua", 1);
        $this->assertEquals([127, 255, 212], Color::hslToRgb(160/360, 100/100, 74.9/100), "aquamarine", 1);
        $this->assertEquals([240, 255, 255], Color::hslToRgb(180/360, 100/100, 97.1/100), "azure", 1);
        $this->assertEquals([245, 245, 220], Color::hslToRgb(60/360, 55.6/100, 91.2/100), "beige", 1);
        $this->assertEquals([255, 228, 196], Color::hslToRgb(33/360, 100/100, 88.4/100), "bisque", 1);
        $this->assertEquals([0, 0, 0], Color::hslToRgb(0/360, 0/100, 0/100), "black", 1);
        $this->assertEquals([255, 235, 205], Color::hslToRgb(36/360, 100/100, 90.2/100), "blanchedalmond", 1);
        $this->assertEquals([0, 0, 255], Color::hslToRgb(240/360, 100/100, 50/100), "blue", 1);
        $this->assertEquals([138, 43, 226], Color::hslToRgb(271/360, 75.9/100, 52.7/100), "blueviolet", 1);
        $this->assertEquals([165, 42, 42], Color::hslToRgb(0/360, 59.4/100, 40.6/100), "brown", 1);
        $this->assertEquals([222, 184, 135], Color::hslToRgb(34/360, 56.9/100, 70/100), "burlywood", 1);
        $this->assertEquals([95, 158, 160], Color::hslToRgb(182/360, 25.5/100, 50/100), "cadetblue", 1);
        $this->assertEquals([127, 255, 0], Color::hslToRgb(90/360, 100/100, 50/100), "chartreuse", 1);
        $this->assertEquals([210, 105, 30], Color::hslToRgb(25/360, 75/100, 47.1/100), "chocolate", 1);
        $this->assertEquals([255, 127, 80], Color::hslToRgb(16/360, 100/100, 65.7/100), "coral", 1);
        $this->assertEquals([100, 149, 237], Color::hslToRgb(219/360, 79.2/100, 66.1/100), "cornflowerblue", 1);
        $this->assertEquals([255, 248, 220], Color::hslToRgb(48/360, 100/100, 93.1/100), "cornsilk", 1);
        $this->assertEquals([220, 20, 60], Color::hslToRgb(348/360, 83.3/100, 47.1/100), "crimson", 1);
        $this->assertEquals([0, 255, 255], Color::hslToRgb(180/360, 100/100, 50/100), "cyan", 1);
        $this->assertEquals([0, 0, 139], Color::hslToRgb(240/360, 100/100, 27.3/100), "darkblue", 1);
        $this->assertEquals([0, 139, 139], Color::hslToRgb(180/360, 100/100, 27.3/100), "darkcyan", 1);
        $this->assertEquals([184, 134, 11], Color::hslToRgb(43/360, 88.7/100, 38.2/100), "darkgoldenrod", 1);
        $this->assertEquals([169, 169, 169], Color::hslToRgb(0/360, 0/100, 66.3/100), "darkgray", 1);
        $this->assertEquals([0, 100, 0], Color::hslToRgb(120/360, 100/100, 19.6/100), "darkgreen", 1);
        $this->assertEquals([169, 169, 169], Color::hslToRgb(0/360, 0/100, 66.3/100), "darkgrey", 1);
        $this->assertEquals([189, 183, 107], Color::hslToRgb(56/360, 38.3/100, 58/100), "darkkhaki", 1);
        $this->assertEquals([139, 0, 139], Color::hslToRgb(300/360, 100/100, 27.3/100), "darkmagenta", 1);
        $this->assertEquals([85, 107, 47], Color::hslToRgb(82/360, 39/100, 30.2/100), "darkolivegreen", 1);
        $this->assertEquals([255, 140, 0], Color::hslToRgb(33/360, 100/100, 50/100), "darkorange", 1);
        $this->assertEquals([153, 50, 204], Color::hslToRgb(280/360, 60.6/100, 49.8/100), "darkorchid", 1);
        $this->assertEquals([139, 0, 0], Color::hslToRgb(0/360, 100/100, 27.3/100), "darkred", 1);
        $this->assertEquals([233, 150, 122], Color::hslToRgb(15/360, 71.6/100, 69.6/100), "darksalmon", 1);
        $this->assertEquals([143, 188, 143], Color::hslToRgb(120/360, 25.1/100, 64.9/100), "darkseagreen", 1);
        $this->assertEquals([72, 61, 139], Color::hslToRgb(248/360, 39/100, 39.2/100), "darkslateblue", 1);
        $this->assertEquals([47, 79, 79], Color::hslToRgb(180/360, 25.4/100, 24.7/100), "darkslategray", 1);
        $this->assertEquals([47, 79, 79], Color::hslToRgb(180/360, 25.4/100, 24.7/100), "darkslategrey", 1);
        $this->assertEquals([0, 206, 209], Color::hslToRgb(181/360, 100/100, 41/100), "darkturquoise", 1);
        $this->assertEquals([148, 0, 211], Color::hslToRgb(282/360, 100/100, 41.4/100), "darkviolet", 1);
        $this->assertEquals([255, 20, 145], Color::hslToRgb(328/360, 100/100, 53.9/100), "deeppink", 1);
        $this->assertEquals([0, 191, 255], Color::hslToRgb(195/360, 100/100, 50/100), "deepskyblue", 1);
        $this->assertEquals([105, 105, 105], Color::hslToRgb(0/360, 0/100, 41.2/100), "dimgray", 1);
        $this->assertEquals([105, 105, 105], Color::hslToRgb(0/360, 0/100, 41.2/100), "dimgrey", 1);
        $this->assertEquals([30, 144, 255], Color::hslToRgb(210/360, 100/100, 55.9/100), "dodgerblue", 1);
        $this->assertEquals([178, 34, 34], Color::hslToRgb(0/360, 67.9/100, 41.6/100), "firebrick", 1);
        $this->assertEquals([255, 250, 240], Color::hslToRgb(40/360, 100/100, 97.1/100), "floralwhite", 1);
        $this->assertEquals([34, 139, 34], Color::hslToRgb(120/360, 60.7/100, 33.9/100), "forestgreen", 1);
        $this->assertEquals([255, 0, 255], Color::hslToRgb(300/360, 100/100, 50/100), "fuchsia", 1);
        $this->assertEquals([220, 220, 220], Color::hslToRgb(0/360, 0/100, 86.3/100), "gainsboro", 1);
        $this->assertEquals([248, 248, 255], Color::hslToRgb(240/360, 100/100, 98.6/100), "ghostwhite", 1);
        $this->assertEquals([255, 217, 0], Color::hslToRgb(51/360, 100/100, 50/100), "gold", 1);
        $this->assertEquals([218, 165, 32], Color::hslToRgb(43/360, 74.4/100, 49/100), "goldenrod", 1);
        $this->assertEquals([128, 128, 128], Color::hslToRgb(0/360, 0/100, 50.2/100), "gray", 1);
        $this->assertEquals([0, 128, 0], Color::hslToRgb(120/360, 100/100, 25.1/100), "green", 1);
        $this->assertEquals([173, 255, 47], Color::hslToRgb(84/360, 100/100, 59.2/100), "greenyellow", 1);
        $this->assertEquals([128, 128, 128], Color::hslToRgb(0/360, 0/100, 50.2/100), "grey", 1);
        $this->assertEquals([240, 255, 240], Color::hslToRgb(120/360, 100/100, 97.1/100), "honeydew", 1);
        $this->assertEquals([255, 105, 180], Color::hslToRgb(330/360, 100/100, 70.6/100), "hotpink", 1);
        $this->assertEquals([205, 92, 92], Color::hslToRgb(0/360, 53.1/100, 58.2/100), "indianred", 1);
        $this->assertEquals([75, 0, 130], Color::hslToRgb(275/360, 100/100, 25.5/100), "indigo", 1);
        $this->assertEquals([255, 255, 240], Color::hslToRgb(60/360, 100/100, 97.1/100), "ivory", 1);
        $this->assertEquals([240, 230, 140], Color::hslToRgb(54/360, 76.9/100, 74.5/100), "khaki", 1);
        $this->assertEquals([230, 230, 250], Color::hslToRgb(240/360, 66.7/100, 94.1/100), "lavender", 1);
        $this->assertEquals([255, 240, 245], Color::hslToRgb(340/360, 100/100, 97.1/100), "lavenderblush", 1);
        $this->assertEquals([126, 252, 0], Color::hslToRgb(90/360, 100/100, 49.4/100), "lawngreen", 1);
        $this->assertEquals([255, 250, 205], Color::hslToRgb(54/360, 100/100, 90.2/100), "lemonchiffon", 1);
        $this->assertEquals([173, 216, 230], Color::hslToRgb(195/360, 53.3/100, 79/100), "lightblue", 1);
        $this->assertEquals([240, 128, 128], Color::hslToRgb(0/360, 78.9/100, 72.2/100), "lightcoral", 1);
        $this->assertEquals([224, 255, 255], Color::hslToRgb(180/360, 100/100, 93.9/100), "lightcyan", 1);
        $this->assertEquals([250, 250, 210], Color::hslToRgb(60/360, 80/100, 90.2/100), "lightgoldenrodyellow", 1);
        $this->assertEquals([211, 211, 211], Color::hslToRgb(0/360, 0/100, 82.7/100), "lightgray", 1);
        $this->assertEquals([144, 238, 144], Color::hslToRgb(120/360, 73.4/100, 74.9/100), "lightgreen", 1);
        $this->assertEquals([211, 211, 211], Color::hslToRgb(0/360, 0/100, 82.7/100), "lightgrey", 1);
        $this->assertEquals([255, 182, 193], Color::hslToRgb(351/360, 100/100, 85.7/100), "lightpink", 1);
        $this->assertEquals([255, 160, 122], Color::hslToRgb(17/360, 100/100, 73.9/100), "lightsalmon", 1);
        $this->assertEquals([32, 178, 170], Color::hslToRgb(177/360, 69.5/100, 41.2/100), "lightseagreen", 1);
        $this->assertEquals([135, 206, 250], Color::hslToRgb(203/360, 92/100, 75.5/100), "lightskyblue", 1);
        $this->assertEquals([119, 136, 153], Color::hslToRgb(210/360, 14.3/100, 53.3/100), "lightslategray", 1);
        $this->assertEquals([119, 136, 153], Color::hslToRgb(210/360, 14.3/100, 53.3/100), "lightslategrey", 1);
        $this->assertEquals([176, 196, 222], Color::hslToRgb(214/360, 41.1/100, 78/100), "lightsteelblue", 1);
        $this->assertEquals([255, 255, 224], Color::hslToRgb(60/360, 100/100, 93.9/100), "lightyellow", 1);
        $this->assertEquals([0, 255, 0], Color::hslToRgb(120/360, 100/100, 50/100), "lime", 1);
        $this->assertEquals([50, 205, 50], Color::hslToRgb(120/360, 60.8/100, 50/100), "limegreen", 1);
        $this->assertEquals([250, 240, 230], Color::hslToRgb(30/360, 66.7/100, 94.1/100), "linen", 1);
        $this->assertEquals([255, 0, 255], Color::hslToRgb(300/360, 100/100, 50/100), "magenta", 1);
        $this->assertEquals([128, 0, 0], Color::hslToRgb(0/360, 100/100, 25.1/100), "maroon", 1);
        $this->assertEquals([102, 205, 170], Color::hslToRgb(160/360, 50.7/100, 60.2/100), "mediumaquamarine", 1);
        $this->assertEquals([0, 0, 205], Color::hslToRgb(240/360, 100/100, 40.2/100), "mediumblue", 1);
        $this->assertEquals([186, 85, 211], Color::hslToRgb(288/360, 58.9/100, 58/100), "mediumorchid", 1);
        $this->assertEquals([147, 112, 219], Color::hslToRgb(260/360, 59.8/100, 64.9/100), "mediumpurple", 1);
        $this->assertEquals([60, 179, 113], Color::hslToRgb(147/360, 49.8/100, 46.9/100), "mediumseagreen", 1);
        $this->assertEquals([123, 104, 238], Color::hslToRgb(249/360, 79.8/100, 67.1/100), "mediumslateblue", 1);
        $this->assertEquals([0, 250, 154], Color::hslToRgb(157/360, 100/100, 49/100), "mediumspringgreen", 1);
        $this->assertEquals([72, 209, 204], Color::hslToRgb(178/360, 59.8/100, 55.1/100), "mediumturquoise", 1);
        $this->assertEquals([199, 21, 133], Color::hslToRgb(322/360, 80.9/100, 43.1/100), "mediumvioletred", 1);
        $this->assertEquals([25, 25, 112], Color::hslToRgb(240/360, 63.5/100, 26.9/100), "midnightblue", 1);
        $this->assertEquals([245, 255, 250], Color::hslToRgb(150/360, 100/100, 98/100), "mintcream", 1);
        $this->assertEquals([255, 228, 225], Color::hslToRgb(6/360, 100/100, 94.1/100), "mistyrose", 1);
        $this->assertEquals([255, 228, 181], Color::hslToRgb(38/360, 100/100, 85.5/100), "moccasin", 1);
        $this->assertEquals([255, 222, 173], Color::hslToRgb(36/360, 100/100, 83.9/100), "navajowhite", 1);
        $this->assertEquals([0, 0, 128], Color::hslToRgb(240/360, 100/100, 25.1/100), "navy", 1);
        $this->assertEquals([253, 245, 230], Color::hslToRgb(39/360, 85.2/100, 94.7/100), "oldlace", 1);
        $this->assertEquals([128, 128, 0], Color::hslToRgb(60/360, 100/100, 25.1/100), "olive", 1);
        $this->assertEquals([107, 142, 35], Color::hslToRgb(80/360, 60.5/100, 34.7/100), "olivedrab", 1);
        $this->assertEquals([255, 165, 0], Color::hslToRgb(39/360, 100/100, 50/100), "orange", 1);
        $this->assertEquals([255, 69, 0], Color::hslToRgb(16/360, 100/100, 50/100), "orangered", 1);
        $this->assertEquals([218, 112, 214], Color::hslToRgb(302/360, 58.9/100, 64.7/100), "orchid", 1);
        $this->assertEquals([238, 232, 170], Color::hslToRgb(55/360, 66.7/100, 80/100), "palegoldenrod", 1);
        $this->assertEquals([152, 251, 152], Color::hslToRgb(120/360, 92.5/100, 79/100), "palegreen", 1);
        $this->assertEquals([175, 238, 238], Color::hslToRgb(180/360, 64.9/100, 81/100), "paleturquoise", 1);
        $this->assertEquals([219, 112, 147], Color::hslToRgb(340/360, 59.8/100, 64.9/100), "palevioletred", 1);
        $this->assertEquals([255, 239, 213], Color::hslToRgb(37/360, 100/100, 91.8/100), "papayawhip", 1);
        $this->assertEquals([255, 218, 185], Color::hslToRgb(28/360, 100/100, 86.3/100), "peachpuff", 1);
        $this->assertEquals([205, 133, 63], Color::hslToRgb(30/360, 58.7/100, 52.5/100), "peru", 1);
        $this->assertEquals([255, 192, 203], Color::hslToRgb(350/360, 100/100, 87.6/100), "pink", 1);
        $this->assertEquals([221, 160, 221], Color::hslToRgb(300/360, 47.3/100, 74.7/100), "plum", 1);
        $this->assertEquals([176, 224, 230], Color::hslToRgb(187/360, 51.9/100, 79.6/100), "powderblue", 1);
        $this->assertEquals([128, 0, 128], Color::hslToRgb(300/360, 100/100, 25.1/100), "purple", 1);
        $this->assertEquals([102, 51, 153], Color::hslToRgb(270/360, 50/100, 40/100), "rebeccapurple", 1);
        $this->assertEquals([255, 0, 0], Color::hslToRgb(0/360, 100/100, 50/100), "red", 1);
        $this->assertEquals([188, 143, 143], Color::hslToRgb(0/360, 25.1/100, 64.9/100), "rosybrown", 1);
        $this->assertEquals([65, 105, 225], Color::hslToRgb(225/360, 72.7/100, 56.9/100), "royalblue", 1);
        $this->assertEquals([139, 69, 19], Color::hslToRgb(25/360, 75.9/100, 31/100), "saddlebrown", 1);
        $this->assertEquals([250, 128, 114], Color::hslToRgb(6/360, 93.2/100, 71.4/100), "salmon", 1);
        $this->assertEquals([244, 164, 96], Color::hslToRgb(28/360, 87.1/100, 66.7/100), "sandybrown", 1);
        $this->assertEquals([46, 139, 87], Color::hslToRgb(146/360, 50.3/100, 36.3/100), "seagreen", 1);
        $this->assertEquals([255, 245, 238], Color::hslToRgb(25/360, 100/100, 96.7/100), "seashell", 1);
        $this->assertEquals([160, 82, 45], Color::hslToRgb(19/360, 56.1/100, 40.2/100), "sienna", 1);
        $this->assertEquals([192, 192, 192], Color::hslToRgb(0/360, 0/100, 75.3/100), "silver", 1);
        $this->assertEquals([135, 206, 235], Color::hslToRgb(197/360, 71.4/100, 72.5/100), "skyblue", 1);
        $this->assertEquals([106, 90, 205], Color::hslToRgb(248/360, 53.5/100, 57.8/100), "slateblue", 1);
        $this->assertEquals([112, 128, 144], Color::hslToRgb(210/360, 12.6/100, 50.2/100), "slategray", 1);
        $this->assertEquals([112, 128, 144], Color::hslToRgb(210/360, 12.6/100, 50.2/100), "slategrey", 1);
        $this->assertEquals([255, 250, 250], Color::hslToRgb(0/360, 100/100, 99/100), "snow", 1);
        $this->assertEquals([0, 255, 127], Color::hslToRgb(150/360, 100/100, 50/100), "springgreen", 1);
        $this->assertEquals([70, 130, 180], Color::hslToRgb(207/360, 44/100, 49/100), "steelblue", 1);
        $this->assertEquals([210, 180, 140], Color::hslToRgb(34/360, 43.8/100, 68.6/100), "tan", 1);
        $this->assertEquals([0, 128, 128], Color::hslToRgb(180/360, 100/100, 25.1/100), "teal", 1);
        $this->assertEquals([216, 191, 216], Color::hslToRgb(300/360, 24.3/100, 79.8/100), "thistle", 1);
        $this->assertEquals([255, 99, 71], Color::hslToRgb(9/360, 100/100, 63.9/100), "tomato", 1);
        $this->assertEquals([64, 224, 208], Color::hslToRgb(174/360, 72.1/100, 56.5/100), "turquoise", 1);
        $this->assertEquals([238, 130, 238], Color::hslToRgb(300/360, 76.1/100, 72.2/100), "violet", 1);
        $this->assertEquals([245, 222, 179], Color::hslToRgb(39/360, 76.7/100, 83.1/100), "wheat", 1);
        $this->assertEquals([255, 255, 255], Color::hslToRgb(0/360, 0/100, 100/100), "white", 1);
        $this->assertEquals([245, 245, 245], Color::hslToRgb(0/360, 0/100, 96.1/100), "whitesmoke", 1);
        $this->assertEquals([255, 255, 0], Color::hslToRgb(60/360, 100/100, 50/100), "yellow", 1);
        $this->assertEquals([154, 205, 50], Color::hslToRgb(80/360, 60.8/100, 50/100), "yellowgreen", 1);
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
            $this->assertEquals($hsl, Color::rgbToHsl($rgb[0], $rgb[1], $rgb[2]), "hsl($hsl[0], $hsl[1], $hsl[2]) -> rgb($rgb[0], $rgb[1], $rgb[2])", 0.05);
        }
    }



    function testHuslToRgb() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEquals($colorspaces['rgb'], Color::huslToRgb(...$colorspaces['husl']), $hex);
        }
    }


    function testRgbToHusl() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEquals($colorspaces['husl'], Color::rgbToHusl(...$colorspaces['rgb']), $hex);
        }
    }

    function testHuslpToRgb() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEquals($colorspaces['rgb'], Color::huslpToRgb(...$colorspaces['huslp']), $hex);
        }
    }

    function testRgbToHuslp() {
        foreach(self::$huslTests as $hex => $colorspaces) {
            $this->assertEquals($colorspaces['huslp'], Color::rgbToHuslp(...$colorspaces['rgb']), $hex);
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
