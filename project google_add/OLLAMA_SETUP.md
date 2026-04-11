# Ollama Model Selection

## Recommended Model

For this project, use **llava** (Large Language and Vision Assistant):

```
ollama pull llava
```

## Alternative Models

| Model | Description | Use Case |
|-------|-------------|----------|
| `llava` | Vision-capable, best for image analysis | Recommended |
| `bakllava` | Faster, open source alternative | Better performance |
| `llama2` | Text only | If vision not needed |
| `mistral` | Text only, good quality | General text tasks |

## How to Check Available Models

```
ollama list
```

## Current Configuration

The default model is set to `llava` in the code:
- Default URL: `http://localhost:11434`
- Default Model: `llava`

You can change the model by setting the environment variable:
```
OLLAMA_MODEL=your_model_name
