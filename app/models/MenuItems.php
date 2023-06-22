<?php
class MenuItems {
    private $items;
    private $processedItems;

    private const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png', ''];
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    public function __construct() {
        $this->items = array();
        $this->processedItems = '';
    }

    public function addItem(string $name, int $price, string $photo): void {
        $item = array(
            'name' => $name,
            'price' => $price,
            'photo' => $photo
        );
        $this->items[] = $item;
    }

    public function processMenuItems($menu_items, $menu_photos, string $resultFolderPath, string $resultFolderName, $isPreview): void {
        foreach ($menu_items as $i => $item) {
            $item_name = htmlspecialchars($item['name'], ENT_QUOTES);
            $item_price = htmlspecialchars($item['price'], ENT_QUOTES);
            $item_photo = $menu_photos['tmp_name'][$i]['photo'];
            $item_photo_type = pathinfo($menu_photos['name'][$i]['photo'], PATHINFO_EXTENSION);
            $item_photo_size = $menu_photos['size'][$i]['photo'];
            if (!in_array($item_photo_type, self::ALLOWED_FILE_TYPES) && !empty($item_photo)) {
                die('Error: Invalid file type.');
            }
    
            if ($item_photo_size > self::MAX_FILE_SIZE && !empty($item_photo)) {
                die('Error: File size exceeds the limit.');
            }
            $this->addItem($item_name, $item_price, $item_photo);
        }

        $input_content = file_get_contents($resultFolderPath . 'index.html');
        $tempitems = '';
  
        foreach ($this->items as $i => $item) {
            $item_number = (int) $i + 1;
            $item_name = htmlspecialchars($item['name'], ENT_QUOTES);
            $item_price = htmlspecialchars($item['price'], ENT_QUOTES);
            $item_photo = $item['photo'];
            $item_photo_file = $resultFolderPath . 'images/menu/item' . $item_number . '.png';
            if ($isPreview) {
            $item_photo_file_path = $resultFolderName . 'images/menu/item' . $item_number . '.png';
            } else {
            $item_photo_file_path = 'images/menu/item' . $item_number . '.png';
            }

            move_uploaded_file($item_photo, $item_photo_file);
            $item_content = <<<EOT
            <li>
              <div>
                <img src="$item_photo_file_path">
              </div>
              <div>
                <span class="name">$item_name</span> </br>
                <span class="price">$item_price UAH</span> </br>
                <button id="item$item_number" class="additembtn" data-price="$item_price">+ (0)</button>
              </div> </br>
            </li>
EOT;
            $tempitems .= "\n$item_content\n";
        }
  
        $input_content = preg_replace('/(?<=<ul class="menu">)(.*?)(?=<\/ul>)/s', $tempitems, $input_content);
        file_put_contents($resultFolderPath . 'index.html', $input_content);
        $this->setProcessedItems($tempitems);
    }

    public function getItems() : array {
        return $this->items;
    }

    public function removeItems() : void {
        $this->items = array();
    }

    public function getProcessedItems() : string {
        return $this->processedItems;
    }
    public function setProcessedItems(string $items): void {
        $this->processedItems = $items;
    }
}
?>