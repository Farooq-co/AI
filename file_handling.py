"""
File handling module for chat bot functionality.
Provides utilities for managing chat history, conversation logs, and data persistence.
"""

import json
import os
import shutil
from datetime import datetime
from pathlib import Path
from typing import Any, Dict, List, Optional


class ChatHistoryManager:
    """Manages chat history storage and retrieval."""
    
    def __init__(self, storage_path: str = "chat_history"):
        """
        Initialize the chat history manager.
        
        Args:
            storage_path: Directory to store chat history files
        """
        self.storage_path = Path(storage_path)
        self.storage_path.mkdir(parents=True, exist_ok=True)
    
    def save_message(self, user_id: str, role: str, content: str, metadata: Optional[Dict] = None) -> str:
        """
        Save a chat message to history.
        
        Args:
            user_id: Unique identifier for the user
            role: Message role (user, assistant, system)
            content: Message content
            metadata: Optional metadata for the message
            
        Returns:
            Message ID
        """
        message_id = f"{user_id}_{datetime.now().timestamp()}"
        
        message_data = {
            "id": message_id,
            "user_id": user_id,
            "role": role,
            "content": content,
            "timestamp": datetime.now().isoformat(),
            "metadata": metadata or {}
        }
        
        # Store in user-specific file
        user_file = self.storage_path / f"{user_id}.json"
        messages = self._load_messages(user_id)
        messages.append(message_data)
        
        with open(user_file, 'w', encoding='utf-8') as f:
            json.dump(messages, f, indent=2, ensure_ascii=False)
        
        return message_id
    
    def _load_messages(self, user_id: str) -> List[Dict]:
        """Load messages for a specific user."""
        user_file = self.storage_path / f"{user_id}.json"
        
        if not user_file.exists():
            return []
        
        try:
            with open(user_file, 'r', encoding='utf-8') as f:
                return json.load(f)
        except (json.JSONDecodeError, IOError):
            return []
    
    def get_history(self, user_id: str, limit: Optional[int] = None) -> List[Dict]:
        """
        Get chat history for a user.
        
        Args:
            user_id: Unique identifier for the user
            limit: Maximum number of messages to retrieve (most recent)
            
        Returns:
            List of message dictionaries
        """
        messages = self._load_messages(user_id)
        
        if limit:
            return messages[-limit:]
        return messages
    
    def clear_history(self, user_id: str) -> bool:
        """
        Clear chat history for a user.
        
        Args:
            user_id: Unique identifier for the user
            
        Returns:
            True if history was cleared, False otherwise
        """
        user_file = self.storage_path / f"{user_id}.json"
        
        if user_file.exists():
            user_file.unlink()
            return True
        return False
    
    def get_all_users(self) -> List[str]:
        """Get list of all users with chat history."""
        return [f.stem for f in self.storage_path.glob("*.json")]


class ConversationLogger:
    """Logs conversations to files for analysis and debugging."""
    
    def __init__(self, log_dir: str = "conversation_logs"):
        """
        Initialize the conversation logger.
        
        Args:
            log_dir: Directory to store conversation logs
        """
        self.log_dir = Path(log_dir)
        self.log_dir.mkdir(parents=True, exist_ok=True)
    
    def log_conversation(self, user_id: str, messages: List[Dict], metadata: Optional[Dict] = None) -> str:
        """
        Log an entire conversation.
        
        Args:
            user_id: Unique identifier for the user
            messages: List of messages in the conversation
            metadata: Optional metadata about the conversation
            
        Returns:
            Log file path
        """
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        log_file = self.log_dir / f"conversation_{user_id}_{timestamp}.json"
        
        log_data = {
            "user_id": user_id,
            "timestamp": datetime.now().isoformat(),
            "message_count": len(messages),
            "metadata": metadata or {},
            "messages": messages
        }
        
        with open(log_file, 'w', encoding='utf-8') as f:
            json.dump(log_data, f, indent=2, ensure_ascii=False)
        
        return str(log_file)
    
    def get_conversation_logs(self, user_id: Optional[str] = None, limit: int = 10) -> List[Dict]:
        """
        Get recent conversation logs.
        
        Args:
            user_id: Optional filter by user ID
            limit: Maximum number of logs to retrieve
            
        Returns:
            List of conversation log summaries
        """
        pattern = f"conversation_{user_id}_*.json" if user_id else "conversation_*.json"
        log_files = sorted(self.log_dir.glob(pattern), key=lambda x: x.stat().st_mtime, reverse=True)
        
        logs = []
        for log_file in log_files[:limit]:
            try:
                with open(log_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    logs.append({
                        "file": str(log_file),
                        "timestamp": data.get("timestamp"),
                        "message_count": data.get("message_count"),
                        "user_id": data.get("user_id")
                    })
            except (json.JSONDecodeError, IOError):
                continue
        
        return logs


class ConfigManager:
    """Manages configuration files for the chat bot."""
    
    def __init__(self, config_file: str = "bot_config.json"):
        """
        Initialize the config manager.
        
        Args:
            config_file: Path to the configuration file
        """
        self.config_file = Path(config_file)
        self._default_config = {
            "bot_name": "ChatBot",
            "max_history_length": 100,
            "response_timeout": 30,
            "enable_logging": True,
            "supported_languages": ["en"],
            "api_keys": {},
            "user_preferences": {}
        }
    
    def load_config(self) -> Dict[str, Any]:
        """
        Load configuration from file.
        
        Returns:
            Configuration dictionary
        """
        if not self.config_file.exists():
            self.save_config(self._default_config)
            return self._default_config.copy()
        
        try:
            with open(self.config_file, 'r', encoding='utf-8') as f:
                return json.load(f)
        except (json.JSONDecodeError, IOError):
            return self._default_config.copy()
    
    def save_config(self, config: Dict[str, Any]) -> bool:
        """
        Save configuration to file.
        
        Args:
            config: Configuration dictionary to save
            
        Returns:
            True if successful, False otherwise
        """
        try:
            with open(self.config_file, 'w', encoding='utf-8') as f:
                json.dump(config, f, indent=2, ensure_ascii=False)
            return True
        except IOError:
            return False
    
    def get(self, key: str, default: Any = None) -> Any:
        """
        Get a configuration value.
        
        Args:
            key: Configuration key
            default: Default value if key not found
            
        Returns:
            Configuration value
        """
        config = self.load_config()
        return config.get(key, default)
    
    def set(self, key: str, value: Any) -> bool:
        """
        Set a configuration value.
        
        Args:
            key: Configuration key
            value: Value to set
            
        Returns:
            True if successful, False otherwise
        """
        config = self.load_config()
        config[key] = value
        return self.save_config(config)


class DataExporter:
    """Exports chat data to various formats."""
    
    def __init__(self, base_path: str = "exports"):
        """
        Initialize the data exporter.
        
        Args:
            base_path: Base directory for exports
        """
        self.base_path = Path(base_path)
        self.base_path.mkdir(parents=True, exist_ok=True)
    
    def export_to_json(self, data: Any, filename: str) -> str:
        """
        Export data to JSON file.
        
        Args:
            data: Data to export
            filename: Name of the export file
            
        Returns:
            Path to exported file
        """
        export_path = self.base_path / filename
        
        with open(export_path, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
        
        return str(export_path)
    
    def export_to_text(self, messages: List[Dict], filename: str) -> str:
        """
        Export chat messages to plain text format.
        
        Args:
            messages: List of message dictionaries
            filename: Name of the export file
            
        Returns:
            Path to exported file
        """
        export_path = self.base_path / filename
        
        with open(export_path, 'w', encoding='utf-8') as f:
            for msg in messages:
                timestamp = msg.get('timestamp', 'Unknown time')
                role = msg.get('role', 'unknown')
                content = msg.get('content', '')
                
                f.write(f"[{timestamp}] {role.upper()}: {content}\n")
                f.write("-" * 50 + "\n")
        
        return str(export_path)
    
    def create_backup(self, source_paths: List[str], backup_name: Optional[str] = None) -> str:
        """
        Create a backup of specified files/directories.
        
        Args:
            source_paths: List of paths to backup
            backup_name: Optional name for the backup
            
        Returns:
            Path to backup directory
        """
        if backup_name is None:
            backup_name = f"backup_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        
        backup_path = self.base_path / backup_name
        backup_path.mkdir(parents=True, exist_ok=True)
        
        for source in source_paths:
            source_path = Path(source)
            if source_path.exists():
                dest = backup_path / source_path.name
                if source_path.is_dir():
                    shutil.copytree(source_path, dest, dirs_exist_ok=True)
                else:
                    shutil.copy2(source_path, dest)
        
        return str(backup_path)


# Utility functions
def ensure_directory(path: str) -> Path:
    """
    Ensure a directory exists, create if it doesn't.
    
    Args:
        path: Directory path
        
    Returns:
        Path object
    """
    dir_path = Path(path)
    dir_path.mkdir(parents=True, exist_ok=True)
    return dir_path


def read_json_file(file_path: str) -> Optional[Dict]:
    """
    Read a JSON file.
    
    Args:
        file_path: Path to JSON file
        
    Returns:
        Parsed JSON data or None on error
    """
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    except (json.JSONDecodeError, IOError, FileNotFoundError):
        return None


def write_json_file(file_path: str, data: Any) -> bool:
    """
    Write data to a JSON file.
    
    Args:
        file_path: Path to JSON file
        data: Data to write
        
    Returns:
        True if successful, False otherwise
    """
    try:
        with open(file_path, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
        return True
    except IOError:
        return False


def get_file_info(file_path: str) -> Optional[Dict]:
    """
    Get file information.
    
    Args:
        file_path: Path to file
        
    Returns:
        Dictionary with file info or None on error
    """
    path = Path(file_path)
    
    if not path.exists():
        return None
    
    stat = path.stat()
    
    return {
        "name": path.name,
        "size": stat.st_size,
        "created": datetime.fromtimestamp(stat.st_ctime).isoformat(),
        "modified": datetime.fromtimestamp(stat.st_mtime).isoformat(),
        "is_file": path.is_file(),
        "is_dir": path.is_dir()
    }


# Example usage
if __name__ == "__main__":
    # Demo: Create instances and use the file handling utilities
    
    # Chat History Manager
    chat_mgr = ChatHistoryManager()
    
    # Save some sample messages
    chat_mgr.save_message("user123", "user", "Hello, how are you?")
    chat_mgr.save_message("user123", "assistant", "I'm doing well, thank you!")
    chat_mgr.save_message("user123", "user", "What can you help me with?")
    
    # Get history
    history = chat_mgr.get_history("user123")
    print(f"Chat history for user123: {len(history)} messages")
    
    # Config Manager
    config_mgr = ConfigManager()
    config = config_mgr.load_config()
    print(f"Bot name: {config.get('bot_name')}")
    
    # Set a preference
    config_mgr.set("user_preferences.dark_mode", True)
    
    # Export chat history to text
    exporter = DataExporter()
    exporter.export_to_text(history, "user123_conversation.txt")
    print("Exported conversation to text file")
