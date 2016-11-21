#include <stdint.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <locale.h>
#include <wctype.h>
#include <wchar.h>

#include <curl/curl.h>
#include "parson.c"

#define arrayCount(array) sizeof(array)/sizeof((array)[0])

typedef int8_t  s8;
typedef int16_t s16;
typedef int32_t s32;
typedef int64_t s64;
typedef s8 b8;

typedef uint8_t  u8;
typedef uint16_t u16;
typedef uint32_t u32;
typedef uint64_t u64;

typedef float  r32;
typedef double r64;

typedef size_t MemIndex;

#define MAX_ADMINS 10
#define MAX_CHAT_WHITELIST 50

typedef struct
{
    MemIndex size;
    u8 *data;
} MemoryChunk;

typedef struct
{
    wchar_t *at;
} Tokenizer;

typedef struct
{
    b8 running;
    char token[1<<7];
    s64 nextUpdateId;
    u32 adminCount;
    s64 admins[MAX_ADMINS];
    u32 chatIdWhitelistCount;
    s64 chatIdWhitelist[MAX_CHAT_WHITELIST];
} Core;

MemIndex writeCallback(char *nextChunk, MemIndex size, MemIndex nmemb, void *userData)
{
    if(!userData)
    {
        fprintf(stderr, "Write function has nowhere to write to.\n");
        return -1;
    }

    MemoryChunk *memory = (MemoryChunk *)userData;
    MemIndex bytes = size*nmemb;

    memory->data = realloc(memory->data, memory->size + bytes + 1);
    if(!memory->data)
    {
        return 0;
    }

    memcpy(memory->data + memory->size, nextChunk, bytes);
    memory->size += bytes;
    memory->data[memory->size] = 0;

    return bytes;
}

MemIndex nullWriteCallback(char *nextChunk, MemIndex size, MemIndex nmemb, void *userData)
{
    MemIndex bytes = size*nmemb;
    return bytes;
}

void eatWhitespace(Tokenizer *tokenizer)
{
    while(*tokenizer->at && iswspace(*tokenizer->at))
    {
        ++tokenizer->at;
    }
}

wchar_t *findNext(wchar_t *start, wchar_t characters[])
{
    wchar_t *at = start;
    while(*at)
    {
        if((!characters && iswspace(*at)) ||
           (characters && wcschr(characters, *at)))
        {
            break;
        }

        ++at;
    }

    return at;
}

wchar_t *findNextWhitespace(wchar_t *start)
{
    return findNext(start, 0);
}

void nextNonWhitespaceSequence(Tokenizer *tokenizer, wchar_t **outputPtr, b8 toLower)
{
    eatWhitespace(tokenizer);

    wchar_t *start = tokenizer->at;
    tokenizer->at = findNextWhitespace(tokenizer->at);

    MemIndex size = tokenizer->at - start;
    *outputPtr = realloc(*outputPtr, (size+1)*sizeof(wchar_t));
    wchar_t *output = *outputPtr;
    for(wchar_t *at = start;
        at < tokenizer->at;
        ++at)
    {
        wchar_t nextChar = *at;
        if(toLower)
        {
            nextChar = towlower(nextChar);
        }
        output[at-start] = nextChar;
    }

    output[size] = '\0';
}

void nextAlphaNumSequence(Tokenizer *tokenizer, wchar_t **outputPtr, b8 toLower)
{
    eatWhitespace(tokenizer);

    wchar_t *start = tokenizer->at;
    while(iswalnum(*tokenizer->at))
    {
        ++tokenizer->at;
    }

    MemIndex size = tokenizer->at - start;
    *outputPtr = realloc(*outputPtr, (size+1)*sizeof(wchar_t));
    wchar_t *output = *outputPtr;
    for(wchar_t *at = start;
        at < tokenizer->at;
        ++at)
    {
        wchar_t nextChar = *at;
        if(toLower)
        {
            nextChar = towlower(nextChar);
        }
        output[at-start] = nextChar;
    }

    output[size] = '\0';
}

void textUntil(Tokenizer *tokenizer, wchar_t **outputPtr, wchar_t characters[],
               b8 toLower)
{
    wchar_t *start = tokenizer->at;
    tokenizer->at = findNext(start, characters);

    MemIndex size = tokenizer->at - start;
    *outputPtr = realloc(*outputPtr, (size+1)*sizeof(wchar_t));
    wchar_t *output = *outputPtr;
    for(wchar_t *at = start;
        at < tokenizer->at;
        ++at)
    {
        wchar_t nextChar = *at;
        if(toLower)
        {
            nextChar = towlower(nextChar);
        }
        output[at-start] = nextChar;
    }

    output[size] = '\0';
}

b8 checkIfAdmin(Core *core, s64 userId)
{
    b8 found = 0;
    for(u32 index = 0;
        index < core->adminCount;
        ++index)
    {
        if(core->admins[index] == userId)
        {
            found = 1;
            break;
        }
    }

    return found;
}

b8 checkChatWhitelist(Core *core, char *type, s64 chatId)
{
    b8 found = 0;
    b8 isPrivate = type && strcmp(type, "private") == 0;
    if(!isPrivate)
    {
        for(u32 index = 0;
            index < core->chatIdWhitelistCount;
            ++index)
        {
            if(core->chatIdWhitelist[index] == chatId)
            {
                found = 1;
                break;
            }
        }
    }

    return found || isPrivate;
}

void getUpdates(Core *core, CURL *request, s64 nextUpdateId, u32 timeout, MemoryChunk *memory)
{
    char url[1<<9];
    snprintf(url, arrayCount(url), "https://api.telegram.org/bot%s/getUpdates?timeout=%d&offset=%ld",
             core->token, timeout, nextUpdateId);

    curl_easy_reset(request);
    curl_easy_setopt(request, CURLOPT_URL, url);
    curl_easy_setopt(request, CURLOPT_WRITEFUNCTION, writeCallback);
    curl_easy_setopt(request, CURLOPT_WRITEDATA, memory);

    memory->size = 0;

    CURLcode errorCode = curl_easy_perform(request);
    if(errorCode != CURLE_OK)
    {
        fprintf(stderr, "An error ocurred getting updates. CURL error code: %d\n", errorCode);
    }
}

void sendMessage(Core *core, CURL *request, s64 chatId, wchar_t wideMessage[])
{
    char messageUtf8[1<<13];
    wcstombs(messageUtf8, wideMessage, arrayCount(messageUtf8));

    char url[1<<9];
    snprintf(url, arrayCount(url), "https://api.telegram.org/bot%s/sendMessage",
             core->token);

    struct curl_slist *headers = 0;
    headers = curl_slist_append(headers, "Content-Type: application/json");

    JSON_Value *rootValue = json_value_init_object();
    JSON_Object *root = json_value_get_object(rootValue);
    json_object_set_number(root, "chat_id", chatId);
    json_object_set_string(root, "text", messageUtf8);
    char *serializedString = json_serialize_to_string(rootValue);

    curl_easy_reset(request);
    curl_easy_setopt(request, CURLOPT_URL, url);
    curl_easy_setopt(request, CURLOPT_WRITEFUNCTION, nullWriteCallback);
    curl_easy_setopt(request, CURLOPT_HTTPHEADER, headers);
    curl_easy_setopt(request, CURLOPT_POSTFIELDS, serializedString);

    curl_easy_perform(request);

    json_free_serialized_string(serializedString);
    json_value_free(rootValue);

    curl_slist_free_all(headers);
}

void leaveChat(Core *core, CURL *request, s64 chatId)
{
    char url[1<<9];
    snprintf(url, arrayCount(url), "https://api.telegram.org/bot%s/leaveChat?chat_id=%ld",
             core->token, chatId);

    curl_easy_reset(request);
    curl_easy_setopt(request, CURLOPT_URL, url);
    curl_easy_setopt(request, CURLOPT_WRITEFUNCTION, nullWriteCallback);

    curl_easy_perform(request);
}

void readConfig(Core *core, char *fileName)
{
    FILE *file = fopen(fileName, "r");

    wchar_t line[1<<13];
    while(fgetws(line, arrayCount(line), file))
    {
        Tokenizer tokenizer = {.at = line};

        wchar_t *key = 0;
        nextAlphaNumSequence(&tokenizer, &key, 1);

        tokenizer.at = findNext(tokenizer.at, L"=") + 1;
        eatWhitespace(&tokenizer);

        if(wcscmp(key, L"token") == 0)
        {
            wchar_t *value;
            textUntil(&tokenizer, &value, L"\n", 0);
            wcstombs(core->token, value, arrayCount(core->token));
            free(value);
        }
        else if(wcscmp(key, L"admins") == 0)
        {
            while(tokenizer.at && core->adminCount < MAX_ADMINS)
            {
                swscanf(tokenizer.at, L"%ld", core->admins + core->adminCount++);
                tokenizer.at = findNext(tokenizer.at, L",");
                if(tokenizer.at)
                {
                    ++tokenizer.at;
                }
            }
        }
        else if(wcscmp(key, L"chatidwhitelist") == 0)
        {
            while(tokenizer.at && core->chatIdWhitelistCount < MAX_CHAT_WHITELIST)
            {
                swscanf(tokenizer.at, L"%ld",
                        core->chatIdWhitelist + core->chatIdWhitelistCount++);
                tokenizer.at = findNext(tokenizer.at, L",");
                if(tokenizer.at)
                {
                    ++tokenizer.at;
                }
            }
        }

        free(key);
    }
}

int main(void)
{
    setlocale(LC_CTYPE, "en_US.UTF-8");

    Core core = {0};
    readConfig(&core, "config.txt");

    MemoryChunk updateMemory = {0};

    curl_global_init(CURL_GLOBAL_ALL);
    CURL *requestHandle = curl_easy_init();

    // NOTE(nox): Clear unseen messages while offline
    for(;;)
    {
        getUpdates(&core, requestHandle, core.nextUpdateId, 0, &updateMemory);

        JSON_Value *root = json_parse_string((char *)updateMemory.data);
        if(!root)
        {
            fprintf(stderr, "Could not parse updates.\n");
            return -1;
        }

        JSON_Array *updatesArray = json_object_get_array(json_value_get_object(root), "result");
        u32 updatesArraySize = json_array_get_count(updatesArray);

        if(!updatesArraySize)
        {
            break;
        }

        for(u32 updateIndex = 0;
            updateIndex < updatesArraySize;
            ++updateIndex)
        {
            JSON_Object *update = json_array_get_object(updatesArray, updateIndex);
            s64 nextUpdateId = json_object_get_number(update, "update_id") + 1;
            if(nextUpdateId > core.nextUpdateId)
            {
                core.nextUpdateId = nextUpdateId;
            }
        }

        json_value_free(root);
    }

    // NOTE(nox): Real startup
    core.running = 1;
    while(core.running)
    {
        getUpdates(&core, requestHandle, core.nextUpdateId, 60, &updateMemory);

        JSON_Value *root = json_parse_string((char *)updateMemory.data);
        if(!root)
        {
            fprintf(stderr, "Could not parse updates.\n");
            return -1;
        }

        JSON_Array *updatesArray = json_object_get_array(json_value_get_object(root), "result");
        u32 updatesArraySize = json_array_get_count(updatesArray);

        for(u32 updateIndex = 0;
            updateIndex < updatesArraySize;
            ++updateIndex)
        {
            JSON_Object *update = json_array_get_object(updatesArray, updateIndex);
            s64 nextUpdateId = json_object_get_number(update, "update_id") + 1;
            if(nextUpdateId > core.nextUpdateId)
            {
                core.nextUpdateId = nextUpdateId;
            }

            JSON_Object *message = json_object_get_object(update, "message");
            char *messageContentUtf8 = (char *)json_object_get_string(message, "text");
            if(!messageContentUtf8)
            {
                // NOTE(nox): Not a text message, move on...
                continue;
            }

            char *chatType = (char *)json_object_dotget_string(message, "chat.type");
            s64 chatId = json_object_dotget_number(message, "chat.id");
            if(!checkChatWhitelist(&core, chatType, chatId))
            {
                leaveChat(&core, requestHandle, chatId);
                continue;
            }

            s64 senderId = json_object_dotget_number(message, "from.id");
            b8 isAdmin = checkIfAdmin(&core, senderId);

            wchar_t messageContent[1<<14];
            u32 messageSize = mbstowcs(messageContent, messageContentUtf8,
                                       arrayCount(messageContent) - 1);
            if(messageSize == (u32)-1)
            {
                fprintf(stderr, "UTF-8 message is corrupted: %s\n", messageContentUtf8);
                continue;
            }
            messageContent[messageSize] = '\0';

            Tokenizer tokenizer = {.at = messageContent};
            wchar_t *word = 0;
            nextNonWhitespaceSequence(&tokenizer, &word, 1);

            // NOTE(nox): Special first word
            if(wcscmp(word, L"afonso") == 0)
            {
                nextNonWhitespaceSequence(&tokenizer, &word, 1);
                if(wcscmp(word, L"shutdown") == 0)
                {
                    if(isAdmin)
                    {
                        sendMessage(&core, requestHandle, chatId, L"Shutting down...");
                        core.running = 0;
                        free(word);
                        break;
                    }
                    else
                    {
                        sendMessage(&core, requestHandle, chatId, L"You don't have permission to do that.");
                    }
                }
                else if(wcscmp(word, L"say") == 0)
                {
                    eatWhitespace(&tokenizer);
                    sendMessage(&core, requestHandle, chatId, tokenizer.at);
                }
            }
            else
            {
                while(*word)
                {
                    // TODO(nox): Do something with the words
                    nextNonWhitespaceSequence(&tokenizer, &word, 1);
                }
            }

            free(word);
        }

        json_value_free(root);
    }

    return 0;
}
