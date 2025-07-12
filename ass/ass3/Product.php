<?php

class Product {

    private $product_id;
    private $name;
    private $category;
    private $description;
    private $price;
    private $rating;
    private $image_name;
    private $quantity;

    public function __construct($product_id, $name, $category, $description,
    $price, $rating, $image_name, $quantity) {
        $this->product_id = $product_id;
        $this->name = $name;
        $this->category = $category;
        $this->description = $description;
        $this->price = $price;
        $this->rating = $rating;
        $this->setImageName($image_name);
        $this->quantity = $quantity;
    }

    public function getProductId() { return $this->product_id; }
    public function getName() { return $this->name; }
    public function getCategory() { return $this->category; }
    public function getDescription() { return $this->description; }
    public function getPrice() { return $this->price; }
    public function getRating() { return $this->rating; }
    public function getImageName() { return $this->image_name; }
    public function getQuantity() { return $this->quantity; }

    public function setProductId($product_id) { $this->product_id = $product_id; }
    public function setName($name) { $this->name = $name; }
    public function setCategory($category) { $this->category = $category; }
    public function setDescription($description) { $this->description = $description; }
    public function setPrice($price) { $this->price = $price; }
    public function setRating($rating) { $this->rating = $rating; }

    public function setImageName($image_name) {
        $extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        if ($extension === 'jpg') {
            $this->image_name = $image_name;
        } else {
            $this->image_name = null; 
        }
    }

    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function displayInTable() {
        $viewLink = "<a href='view.php?id={$this->product_id}'>{$this->product_id}</a>";
        $editAction = "<a href='edit.php?id={$this->product_id}'><button><img src='editProductInfo.png' alt='Edit' width='40'></button></a>";
        $deleteAction = "<a href='delete.php?id={$this->product_id}' onclick='return confirm(\"Delete this product?\");'><button><img src='deleteProduct.png' alt='Delete' width='40'></button></a>";
        $imagePath = (!empty($this->image_name) && file_exists("images/{$this->image_name}")) ? "images/{$this->image_name}" : "images/default.jpg";
        $imageTag = "<img src='{$imagePath}' alt='{$this->name}' style='width: 100px; height: 100px; object-fit: cover;'>";

        return "<tr>
                    <td style='width: 100px; height: 100px;'>{$imageTag}</td>
                    <td>{$viewLink}</td>
                    <td>{$this->name}</td>
                    <td>{$this->category}</td>
                    <td>\${$this->price}</td>
                    <td>{$this->quantity}</td>
                    <td class='actions'>{$editAction} {$deleteAction}</td>
                </tr>";
    }

    public function displayProductPage() {
        $imagePath = (!empty($this->image_name) && file_exists("images/{$this->image_name}")) ? "images/{$this->image_name}" : "images/default.jpg";
        $imageTag = "<img src='{$imagePath}' alt='{$this->name}' style='width: 250px; height: 250px; object-fit: cover;'>";

        return "<main>
                    <blockquote>
                        {$imageTag}
                        <h2>Product ID: {$this->product_id}, {$this->name}</h2>
                        <ul>
                            <li>Price: \${$this->price}</li>
                            <li>Category: {$this->category}</li>
                            <li>Rating: {$this->rating}/5</li>
                        </ul>
                        <h3>Description:</h3>
                        <p>{$this->description}</p>
                    </blockquote>
                </main>";
    }

    public function displayAsCard() {
        $quantityClass = $this->quantity <= 5 ? 'low' : 'normal';
        $safeName = htmlspecialchars($this->name);
        $safeDescription = htmlspecialchars($this->description);
        $safeCategory = htmlspecialchars($this->category);
        $safeImage = htmlspecialchars($this->image_name);
        $safeID = htmlspecialchars($this->product_id);
        $safePrice = htmlspecialchars($this->price);

        return "
        <div class='product-card'>
            <img src='images/{$safeImage}' alt='{$safeName}'>
            <h3>{$safeID}</h3>
            <button class='product-name' tabindex='0'>{$safeName}</button>
            <div class='tooltip'>
                <section>
                    <h2 class='{$quantityClass}'>In Stock: {$this->quantity}</h2>
                    <p>{$safeDescription}</p>
                </section>
            </div>
            <span class='category-badge'>{$safeCategory}</span>
            <div class='price'>\${$safePrice}</div>
            <nav>
                <form action='view.php' method='get' style='display:inline;'>
                    <input type='hidden' name='id' value='{$safeID}'>
                    <button class='view-btn' type='submit'>View</button>
                </form>
                <form action='cart.php' method='post' style='display:inline;'>
                    <input type='hidden' name='id' value='{$safeID}'>
                    <button class='add-cart-btn' type='submit'>Add to Cart</button>
                </form>
            </nav>
        </div>";
    }
}

?>
