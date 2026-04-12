import numpy as np
# Create a 1D array
arr = np.array([1, 2, 3, 4, 5])
print(arr)

# Create a random array
random_arr = np.random.rand(3, 3)
print("Random 3x3 array:")
print(random_arr)

# Perform some operations
squared = arr ** 2
print("Squared array:", squared)

# Matrix multiplication
matrix1 = np.random.randint(0, 10, (2, 3))
matrix2 = np.random.randint(0, 10, (3, 2))
product = np.dot(matrix1, matrix2)
print("Matrix product:")
print(product)

# Statistical operations
mean_val = np.mean(arr)
std_val = np.std(arr)
print(f"Mean: {mean_val}, Standard Deviation: {std_val}")