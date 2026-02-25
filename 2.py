"""
Famous Random Code - No external dependencies required!
A collection of famous algorithms and fun simulations.
"""

import random
import time
from typing import List, Tuple

# ============================================================
# 1. CONWAY'S GAME OF LIFE - Famous Cellular Automaton
# ============================================================
class GameOfLife:
    """Conway's Game of Life - A cellular automaton"""
    
    def __init__(self, width: int, height: int):
        self.width = width
        self.height = height
        self.grid = [[random.choice([0, 1]) for _ in range(width)] for _ in range(height)]
    
    def count_neighbors(self, x: int, y: int) -> int:
        count = 0
        for i in [-1, 0, 1]:
            for j in [-1, 0, 1]:
                if i == 0 and j == 0:
                    continue
                nx, ny = (x + i) % self.width, (y + j) % self.height
                count += self.grid[ny][nx]
        return count
    
    def step(self) -> None:
        new_grid = []
        for y in range(self.height):
            row = []
            for x in range(self.width):
                neighbors = self.count_neighbors(x, y)
                if self.grid[y][x] == 1:
                    row.append(1 if neighbors in [2, 3] else 0)
                else:
                    row.append(1 if neighbors == 3 else 0)
            new_grid.append(row)
        self.grid = new_grid
    
    def display(self) -> str:
        lines = ["".join("█" if cell else " " for cell in row) for row in self.grid]
        return "\n".join(lines)


# ============================================================
# 2. FIBONACCI - Famous Mathematical Sequence
# ============================================================
def fibonacci(n: int) -> int:
    """Classic Fibonacci using recursion (naive)"""
    if n <= 1:
        return n
    return fibonacci(n - 1) + fibonacci(n - 2)

def fibonacci_memo(n: int, memo: dict = None) -> int:
    """Fibonacci with memoization (efficient)"""
    if memo is None:
        memo = {}
    if n in memo:
        return memo[n]
    if n <= 1:
        return n
    memo[n] = fibonacci_memo(n - 1, memo) + fibonacci_memo(n - 2, memo)
    return memo[n]

def fibonacci_iterative(n: int) -> int:
    """Fibonacci using iteration (most efficient)"""
    if n <= 1:
        return n
    a, b = 0, 1
    for _ in range(2, n + 1):
        a, b = b, a + b
    return b


# ============================================================
# 3. BINARY SEARCH - Famous Algorithm
# ============================================================
def binary_search(arr: List[int], target: int) -> int:
    """Classic binary search - O(log n)"""
    left, right = 0, len(arr) - 1
    while left <= right:
        mid = (left + right) // 2
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1
    return -1


# ============================================================
# 4. QUICKSORT - Famous Sorting Algorithm
# ============================================================
def quicksort(arr: List[int]) -> List[int]:
    """Classic quicksort algorithm"""
    if len(arr) <= 1:
        return arr
    pivot = arr[len(arr) // 2]
    left = [x for x in arr if x < pivot]
    middle = [x for x in arr if x == pivot]
    right = [x for x in arr if x > pivot]
    return quicksort(left) + middle + quicksort(right)


# ============================================================
# 5. MONTE CARLO PI SIMULATION
# ============================================================
def monte_carlo_pi(num_points: int = 10000) -> float:
    """Estimate Pi using Monte Carlo simulation"""
    inside_circle = 0
    for _ in range(num_points):
        x, y = random.random(), random.random()
        if x**2 + y**2 <= 1:
            inside_circle += 1
    return (inside_circle / num_points) * 4


# ============================================================
# 6. PASSWORD GENERATOR
# ============================================================
def generate_password(length: int = 16) -> str:
    """Generate a random secure password"""
    chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()"
    return ''.join(random.choice(chars) for _ in range(length))


# ============================================================
# 7. DIJKSTRA'S ALGORITHM - Famous Path Finding
# ============================================================
def dijkstra(graph: dict, start: str) -> dict:
    """Dijkstra's shortest path algorithm"""
    distances = {node: float('inf') for node in graph}
    distances[start] = 0
    unvisited = set(graph.keys())
    
    while unvisited:
        current = min(unvisited, key=lambda x: distances[x])
        if distances[current] == float('inf'):
            break
        
        for neighbor, weight in graph.get(current, {}).items():
            distance = distances[current] + weight
            if distance < distances[neighbor]:
                distances[neighbor] = distance
        
        unvisited.remove(current)
    
    return distances


# ============================================================
# 8. MANDELBROT SET - Famous Fractal
# ============================================================
def mandelbrot(width: int, height: int, max_iter: int = 100) -> List[List[int]]:
    """Generate Mandelbrot set"""
    result = []
    for y in range(height):
        row = []
        for x in range(width):
            c = complex((x - width/2) / (width/4), (y - height/2) / (height/4))
            z = 0
            for i in range(max_iter):
                if abs(z) > 2:
                    break
                z = z*z + c
            row.append(i if abs(z) <= 2 else 0)
        result.append(row)
    return result


# ============================================================
# 9. SHA-256 SIMPLIFIED ( Educational )
# ============================================================
def simple_hash(message: str) -> str:
    """A simple hash function (not cryptographic, for educational purposes)"""
    hash_val = 0
    for i, char in enumerate(message):
        hash_val = (hash_val * 31 + ord(char)) % (10**9 + 7)
    return f"hash_{hash_val:09d}"


# ============================================================
# DEMO - Run all the famous code
# ============================================================
if __name__ == "__main__":
    print("=" * 60)
    print("🎯 FAMOUS RANDOM CODE - No Dependencies Required!")
    print("=" * 60)
    
    # Fibonacci
    print("\n📊 FIBONACCI SEQUENCE (first 15):")
    print([fibonacci_iterative(i) for i in range(15)])
    
    # Binary Search
    arr = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19]
    print(f"\n🔍 BINARY SEARCH in {arr}:")
    print(f"   Searching for 7: index = {binary_search(arr, 7)}")
    print(f"   Searching for 10: index = {binary_search(arr, 10)}")
    
    # Quicksort
    unsorted = [64, 34, 25, 12, 22, 11, 90, 5, 77, 30]
    print(f"\n⚡ QUICKSORT:")
    print(f"   Original: {unsorted}")
    print(f"   Sorted:   {quicksort(unsorted)}")
    
    # Monte Carlo Pi
    print("\n🎲 MONTE CARLO PI ESTIMATION:")
    for n in [100, 1000, 10000]:
        pi_est = monte_carlo_pi(n)
        print(f"   Points: {n:5d} -> Pi ≈ {pi_est:.6f} (error: {abs(pi_est - 3.14159):.6f})")
    
    # Password Generator
    print("\n🔐 RANDOM PASSWORD GENERATOR:")
    for _ in range(3):
        print(f"   {generate_password(12)}")
    
    # Dijkstra's Algorithm
    graph = {
        'A': {'B': 4, 'C': 2},
        'B': {'C': 1, 'D': 5},
        'C': {'D': 8, 'E': 10},
        'D': {'E': 2, 'F': 6},
        'E': {'F': 3},
        'F': {}
    }
    print("\n🗺️  DIJKSTRA'S ALGORITHM:")
    print(f"   Graph: {graph}")
    print(f"   Shortest paths from A: {dijkstra(graph, 'A')}")
    
    # Simple Hash
    print("\n🔑 SIMPLE HASH FUNCTION:")
    for word in ["hello", "world", "python", "famous"]:
        print(f"   hash('{word}') = {simple_hash(word)}")
    
    # Mandelbrot (small demo)
    print("\n🌀 MANDELBROT SET (5x5 sample):")
    mb = mandelbrot(5, 5, max_iter=10)
    for row in mb:
        print("   " + " ".join(f"{v:3d}" for v in row))
    
    print("\n" + "=" * 60)
    print("✅ All famous code executed without any dependencies!")
    print("=" * 60)
